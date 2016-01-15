<?php
namespace app\components\behaviors;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;

class DirectoryBehavior extends Behavior
{
    /**
     * Upload path to directory
     * @var string
     */
    public $uploadPath = 'fileManager';
    /**
     * Directory name new path
     * @var string
     */
    public $directoryName = null;
    /**
     * Directory Path
     * @var string
     */
    public $directoryPath = null;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
        ];
    }

    public function beforeInsert($event){
        if ($this->getDirectoryName() !== null) {
            if (file_exists($this->getFullPath())) {
                $this->owner->addError('Error create direcotory', Yii::t('Admin', 'Directory exist'));
                $event->isValid = false;
            }

            if (mkdir($this->getFullPath())) {
                $this->owner->path = $this->getSavePath();
                $this->owner->name = $this->getDirectoryName();
                $this->owner->created_at = time();
                $this->owner->modified_at = time();
                $event->isValid = true;

                return true;
            }

            $this->owner->addError('create direcotory', Yii::t('Admin', 'Unable to create directory to server'));
            $event->isValid = false;
        }

        $this->owner->addError('uploadFile', Yii::t('Admin', 'directoryName is empty'));
        $event->isValid = false;

        return false;

    }

    private function getSavePath()
    {
        return !empty($this->directoryPath)
            ? $this->getDirectoryPath() . $this->getDirectoryName()
            :  $this->getDirectoryName();
    }

    private function getFullPath()
    {
        return $this->getWebRootPath() .'/'. $this->getUploadPath(). $this->getDirectoryPath(). '/'. $this->getDirectoryName();
    }

    private function getDirectoryName()
    {
        return $this->directoryName;
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
        $directoryPath = null;
        if ($this->directoryPath !== null)  {
            $directoryPath = '/' .$this->directoryPath .'/';
        }

        return $directoryPath;
    }

}
