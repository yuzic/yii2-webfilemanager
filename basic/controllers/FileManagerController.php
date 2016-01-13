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
use yii\helpers\Url;
use yii\web\HttpException;


class FileManagerController extends Controller
{
    /**
     * Path to upload
     * @var string
     */
    protected $uploadPath = 'fileManager';

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
        $request = Yii::$app->request;
        $headers = Yii::$app->response->headers;
        $model = new FileDirectory();

        $fileDirectory = FileDirectory::findOne((int) $request->post('parent_id'));
        $model->attachBehavior('DirectorySave', array(
            'class' => DirectoryBehavior::className(),
            'directoryName' => $request->post('name'),
            'directoryPath' => $fileDirectory->path,
        ));


        if ($request->post('name') !== null)
        {
            $model->attributes = $request->post();
            $model->parent_id= $fileDirectory->id;

            if ($model->save())
            {
                $headers->add('HTTP/1.1 201 Created','');
               // $headers->add('Location', Url::toRoute('//directory/view', ['id' => $model->id]));
            }
            else {
                $headers->add('HTTP/1.1 400 Bad request','');
            }
        }
        if ($request->isAjax)
        {
            return $this->renderAjax('create_json', ['model' => $model]);
        }

    }

    public function actionCreateFile()
    {
        $model = new FileList();
        $request = Yii::$app->request;
        $headers = Yii::$app->response->headers;
        $fileDirectory = FileDirectory::findOne((int) $_POST['File']['file_directory_id']);
        $model->attachBehavior('FileSave', [
            'class' => FileMoveBehavior::className(),
            'directoryPath' => $fileDirectory->path,
        ]);

        if ($request->post('File') !== null)
        {
            $model->attributes = $request->post('File');
            $model->file_directory_id= $fileDirectory->id;

            if ($model->save())
            {
                $headers->add('HTTP/1.1 201 Created','');
                //$headers->add('Location', Url::toRoute('//file/view'));
            }
            else {
                $headers->add('HTTP/1.1 400 Bad request','');
            }
        }else{
            throw new HttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
        }
        if ($request->isAjax) {
            return $this->renderAjax('createFile_json', array('model' => $model));
        }
    }

    public function actionDeleteDirectory()
    {
        $request = Yii::$app->request;
        if ($request->post('id'))
        {
            $headers = Yii::$app->response->headers;
            $id  = (int) $request->post('id');
            $model = FileDirectory::findOne($id);
            if ($model === null)
            {
                throw new HttpException(404, Yii::t('Admin', 'Directory not found'));
            }
            $path = Yii::getAlias('@webroot') . '/' . $this->uploadPath . '/'. $model->path;
            //$this->deleteDir($path);
            if (!$model->delete())
            {
                $headers->add('HTTP/1.1 400 Bad request','');
                return $this->renderAjax('deleteFile_json', ['model' => $model, 'status' => false]);
            }
            if ($request->isAjax)
            {
                $headers->add('HTTP/1.1 201 Created','');

                return $this->renderAjax('deleteFile_json', ['model' => $model, 'status' => true]);
            }
        } else {
            throw new HttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
        }
    }

    public function actionDeleteFile()
    {
        $request = Yii::$app->request;
        $headers = Yii::$app->response->headers;

        if ($request->post('id')) {
            $id  = (int) $request->post('id');

            $model = FileList::findOne($id);

            if ($model === null)
            {
                throw new \yii\web\HttpException(404, Yii::t('Admin', 'File not found'));
            }

            if ($model->delete())
            {
                $this->deleteFile($model->directory->path. '/' .$model->name);
                $headers->add('HTTP/1.1 201 Created','');
            }
            else {
                $headers->add('HTTP/1.1 400 Bad request','');
            }

            if ($request->isAjax)
            {
                $headers->add('HTTP/1.1 201 Created','');
                return $this->renderAjax('deleteFile_json', ['model' => $model, 'status' => true]);
            }
        }
        else {
            throw new HttpException(403, Yii::t('yii','You are not authorized to perform this action.'));
        }
    }

    public function actionListFile()
    {
        $request = Yii::$app->request;
        $directoryId = (int) ($request->post('directory_id') ? $request->post('directory_id') : 1);
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
        $path = Yii::getAlias('@webroot') . '/' .$this->uploadPath .'/'.$path;

        return unlink($path);
    }

    /**
     * delete recursive path and files
     * @param string $path - local path to directory
     * @return bool
     */
    public function deleteDir($path){
        $uploadPath = Yii::getAlias('@webroot') . '/' .$this->uploadPath;
        if ($path !== $uploadPath)
        {
            $callback = [__CLASS__, __FUNCTION__];
            return is_file($path)
                ? unlink($path)
                : array_map($callback, glob($path.'/*')) == rmdir($path);
        }
    }
}