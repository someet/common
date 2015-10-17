<?php

use yii\db\Schema;
use yii\db\Migration;

class m151012_091430_update_special_table extends Migration
{
    public function safeUp()
    {
        $sql = <<<SQL
    ALTER TABLE `special`
CHANGE COLUMN `title` `title` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '标题' ,
CHANGE COLUMN `desc` `desc` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT '' COMMENT '描述' ,
CHANGE COLUMN `poster` `poster` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NOT NULL COMMENT '海报' ;
SQL;
        $this->execute($sql);
        return true;
    }

    public function safeDown()
    {
        echo "m151012_091430_update_special_table cannot be reverted.\n";

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
