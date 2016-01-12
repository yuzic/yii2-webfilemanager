<?php

use yii\db\Schema;
use yii\db\Migration;

class m160111_095446_file_directory extends Migration
{
    public function up()
    {
        $sql = 'CREATE TABLE "file_directory"
                (
                  "id" serial NOT NULL,
                  "parent_id" INTEGER DEFAULT NULL references file_directory(id),
                  "path" CHARACTER VARYING(500) NOT NULL,
                  "name"  CHARACTER VARYING(500) NOT NULL,
                  "created_at" INTEGER NOT NULL,
                  "modified_at" INTEGER DEFAULT NULL,
                  CONSTRAINT file_directory_pkey PRIMARY KEY (id )
                )';

        $this->execute($sql);
    }

    public function down()
    {
        echo "m160111_095446_file_directory cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
