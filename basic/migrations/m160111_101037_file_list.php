<?php

use yii\db\Schema;
use yii\db\Migration;

class m160111_101037_file_list extends Migration
{
    public function up()
    {
        $sql = 'CREATE TABLE "file_list"
                (
                  "id" serial NOT NULL,
                  "file_directory_id" INTEGER NOT NULL REFERENCES "file_directory" ("id"),
                  "name"  CHARACTER VARYING(500) NOT NULL,
                  "size"  INTEGER NOT NULL,
                  "remote_ip"  INTEGER NOT NULL,
                  "created_at" INTEGER NOT NULL,
                  "modified_at" INTEGER DEFAULT NULL,
                  "status_id" INTEGER NOT NULL,
                  CONSTRAINT file_list_pkey PRIMARY KEY (id )
                )';
        $this->execute($sql);
    }

    public function down()
    {
        echo "m160111_101037_file_list cannot be reverted.\n";

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
