<?php
/**
 * Created by PhpStorm.
 * User: itcoder
 */
use app\components\behaviors\DirectoryBehavior;
use app\components\behaviors\FileMoveBehavior;
use \app\models\FileDirectory;
use \app\models\FileList;

class BehaviorFileListTest extends \yii\codeception\TestCase
{
    public $appConfig = '@tests/unit/_config.php';

    protected static $directoryName = 'one';

    protected static $directoryPath = 'fileManager/files';

    public function testIssetFile()
    {
        $model = FileList::findOne(2);

        $this->assertEquals(2 ,$model->id);
    }


//    public function testSaveFile()
//    {
//
//
//        $model = FileList::findOne((int) 1);
//        $fileDirectory = FileDirectory::findOne((int) 1);
//
//        $model = new FileList();
//
//        $_FILES = [
//            'File'    =>  [
//                'name'      =>  [
//                    'uploadFile'    => 'pgadmin.log',
//                ],
//                'tmp_name'  =>  [
//                    'uploadFile'    => '/tmp'
//                ],
//                'type'      =>  'text/x-log',
//                'size'      =>  [
//                    'uploadFile'    => 144365
//                ],
//                'error'     =>  [
//                    'uploadFile'    => 0
//                ]
//            ]
//        ];
//
//        $model->attachBehavior('FileSave', [
//            'class' => FileMoveBehavior::className(),
//            'directoryPath' => $fileDirectory->path,
//        ]);
//
//        $this->assertTrue($model->save());
//
//    }


}