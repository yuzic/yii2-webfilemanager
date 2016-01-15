<?php
/**
 * Created by PhpStorm.
 * User: itcoder
 */
use app\components\behaviors\DirectoryBehavior;
use app\components\behaviors\FileMoveBehavior;
use \app\models\FileDirectory;
use \app\models\FileList;

class BehaviorFileDirectoryTest extends \yii\codeception\TestCase
{
    public $appConfig = '@tests/unit/_config.php';

    protected static $directoryName = 'one';

    protected static $directoryPath = 'fileManager/files';

    public function testIssetDirectory()
    {
        $fileDirectory = FileDirectory::findOne(2);

        $this->assertEquals(2 ,$fileDirectory->id);
    }


    public function testSaveDirectory()
    {
        $directory = new FileDirectory();

        $fileDirectory = FileDirectory::findOne((int) 1);

        $directory->attachBehavior('DirectorySave', array(
            'class' => DirectoryBehavior::className(),
            'directoryName' => self::$directoryName,
            'directoryPath' => $fileDirectory->path,
        ));

        $directory->parent_id= $fileDirectory->id;

        $this->assertTrue($directory->save());

    }


    public static function tearDownAfterClass()
    {
        rmdir(Yii::getAlias('@webroot') . '/'  .self::$directoryPath . '/' . self::$directoryName);
    }

}