<?php
namespace app\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Controller;
use app\models\FileDirectory;
use app\models\FileList;
use app\components\behaviors\DirectoryBehavior;
use app\components\behaviors\FileMoveBehavior;


class FileManagerController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionCreateDirectory()
    {
        $model = new FileDirectory();
        $fileDirectory = FileDirectory::model()->findByPk((int) $_POST['parentId']);
        $model->attachBehavior('DirectorySave', array(
            'class' => 'ext.DirectoryBehavior',
            'directoryName' => $_POST['name'],
            'directoryPath' => $fileDirectory->path,
        ));
        if (Yii::app()->request->getPost('name') !== null)
        {
            $model->attributes = $_POST;
            if ($model->save())
            {
                $this->headers['HTTP/1.1 201 Created'] = '';
                $this->headers['Location'] = $this->createAbsoluteUrl('//directory/view', array('id' => $model->id));
            }
            else {
                $this->headers['HTTP/1.1 400 Bad request'] = '';
            }
        }
        if (Yii::app()->request->isAjaxRequest)
        {
            $this->renderAjax('create_json', array('model' => $model));
        }

    }

    public function actionCreateFile()
    {
        $model = new FileList();
        $fileDirectory = FileDirectory::model()->findByPk((int) $_POST['File']['directoryId']);
        $model->attachBehavior('FileSave', [
            'class' => FileMoveBehavior::className(),
            'directoryPath' => $fileDirectory->path,
        ]);
        if (Yii::app()->request->getPost('File') !== null)
        {
            $model->attributes = Yii::app()->request->getPost('File');
            $model->remoteIp = ip2long($_SERVER['REMOTE_ADDR']);
            if ($model->save())
            {
                $this->headers['HTTP/1.1 201 Created'] = '';
                $this->headers['Location'] = $this->createAbsoluteUrl('//file/view');
            }
            else{
                $this->headers['HTTP/1.1 400 Bad request'] = '';
            }
        }else{
            throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
        }
        if (Yii::app()->request->isAjaxRequest) {
            $this->renderAjax('createFile_json', array('model' => $model));
        }
    }

    public function actionDeleteDirectory()
    {
        if (isset($_POST['id']))
        {
            $id  = (int) $_POST['id'];
            $model = FileDirectory::model()->findByPk($id);
            if ($model === null)
            {
                throw new CHttpException(404, Yii::t('Admin', 'File not found'));
            }
            $path = Yii::getPathOfAlias('webroot') . '/' .$this->uploadPath .'/'.$model->path;
            $this->deleteDir($path);
            if (!$model->delete())
            {
                $this->headers['HTTP/1.1 400 Bad request'] = '';
                $this->renderAjax('deleteFile_json', array('model' => $model, 'status' => false));
            }
            if (Yii::app()->request->isAjaxRequest)
            {
                $this->headers['HTTP/1.1 201 Created'] = '';
                $this->renderAjax('deleteFile_json', array('model' => $model, 'status' => true));
            }
        }else {
            throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
        }
    }

    public function actionDeleteFile()
    {
        if (isset($_POST['id'])){
            $id  = (int) $_POST['id'];
            $model = File::model()->findByPk($id);
            if ($model === null)
            {
                throw new CHttpException(404, Yii::t('Admin', 'File not found'));
            }
            $this->deleteFile($model->directory->path. '/' .$model->name);
            if ($model->delete())
            {
                $this->headers['HTTP/1.1 201 Created'] = '';
            }
            else{
                $this->headers['HTTP/1.1 400 Bad request'] = '';
            }
            if (Yii::app()->request->isAjaxRequest)
            {
                $this->headers['HTTP/1.1 201 Created'] = '';
                $this->renderAjax('deleteFile_json', array('model' => $model, 'status' => true));
            }
        }
        else {
            throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
        }
    }

    public function actionListFile()
    {
        $directoryId = (int) (isset($_POST['directoryId']) ? $_POST['directoryId'] : 2);
        $headers = Yii::$app->response->headers;

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return  [
            'model' => $this->listFileFromDirectory($directoryId)
        ];
    }

    /**
     * List file and directory
     * @param int $id - id Directory
     * @return array
     */
    private function listFileFromDirectory($id)
    {
        $directoryList = FileDirectory::findAll([
                'parent_id' => $id,
            ]
        );
        $fileList = FileList::findAll([
            'file_directory_id' => $id,
        ]);

       // print_r($fileList);

        $dataFileCollection = [];

        foreach ($directoryList as $directory) {
            $dataFileCollection[] = $directory->jsonSerialize();
        }
        foreach ($fileList as $file) {
            $dataFileCollection[] = $file->jsonSerialize();
        }

        return $dataFileCollection;
    }

    /**
     * delete file from path
     * @param string $path
     * @return bool
     */
    public function deleteFile($path){
        $path = Yii::getPathOfAlias('webroot') . '/' .$this->uploadPath .'/'.$path;

        return unlink($path);
    }

    /**
     * delete recursive path and files
     * @param string $path - local path to directory
     * @return bool
     */
    public function deleteDir($path){
        $uploadPath = Yii::getPathOfAlias('webroot') . '/' .$this->uploadPath;
        if ($path !== $uploadPath)
        {
            $callback = array(__CLASS__, __FUNCTION__);
            return is_file($path)
                ? unlink($path)
                : array_map($callback, glob($path.'/*')) == rmdir($path);
        }
    }
}