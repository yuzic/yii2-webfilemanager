<?php
namespace app\models;

use yii\db\ActiveRecord;
/**
 * Class PhotoGallery
 * @package app\models
 * @property String $id
 * @property String $title
 * @property String $id_user
 * @property String $created
 * @property String $modified
 */
class FileDirectory extends ActiveRecord implements \JsonSerializable
{
    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'file_directory';
    }

    /**
     * Convert data to array
     * @return array
     */
    public function jsonSerialize() {
        return [
            'id' => $this->id === null ? null : intval($this->id),
            'path' =>$this->directory->path,
            'name'     => $this->name,
            'type'     => 'file',
        ];
    }
}