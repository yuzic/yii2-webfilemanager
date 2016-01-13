<?php
namespace app\components\behaviors;

use yii\db\ActiveRecord;
use yii\base\Behavior;
use Yii;


class FileMoveBehavior extends Behavior
{
    /**
     * Upload path  Files
     * @var string
     */
    public $uploadPath = 'fileManager';
    /**
     * Directory Path  files
     * @var string
     */
    public $directoryPath = null;


    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
        ];
    }

    public function beforeInsert($event)
    {
        $fileName = $_FILES['File']['name']['uploadFile'];
        if (move_uploaded_file($_FILES['File']['tmp_name']['uploadFile'], $this->getFullPath() .'/'. $fileName ))
        {
            $this->owner->name = $fileName;
            $this->owner->size = filesize($this->getFullPath() .'/'. $fileName);
            $this->owner->remote_ip = ip2long($_SERVER['REMOTE_ADDR']);
            $this->owner->status_id = 1;
            $this->owner->created_at = time();
            $this->owner->modified_at= time();

            $event->isValid = true;

            return true;
        }
        else
        {
            $this->owner->addError('uploadFile', Yii::t('Admin', 'Unable to save file to server'));
            $event->isValid = false;
            return false;
        }
    }

    private function getFullPath()
    {
        return $this->getWebRootPath() .'/'. $this->getUploadPath() . $this->getDirectoryPath();
    }

    private function getWebRootPath()
    {
        return Yii::getAlias('@webroot');
    }

    private function getUploadPath()
    {
        return $this->uploadPath;
    }

    private function getDirectoryPath()
    {
        if ($this->directoryPath !== null)
            $this->directoryPath = '/' . $this->directoryPath;

        return $this->directoryPath;
    }

}
