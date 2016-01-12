<?php
namespace app\models;

use yii\db\ActiveRecord;
use app\models\FileDirectory;
/**
 * Class PhotoGallery
 * @package app\models
 * @property String $id
 * @property String $title
 * @property String $id_user
 * @property String $created
 * @property String $modified
 */
class FileList extends ActiveRecord implements \JsonSerializable
{
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'file_list';
    }

    public function getDirectory()
    {
        return $this->hasOne(FileDirectory::className(), ['id' => 'file_directory_id']);
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id === null ? null : intval($this->id),
            'path' => $this->directory->path,
            'name'     => $this->name,
            'type'     => 'file',
        ];
    }
}