<?php

use yii\db\Schema;
use yii\db\Migration;

class m151109_060741_update_activity_status_and_question_title_desc extends Migration
{
    public function up()
    {
        $sql = <<<SQL

ALTER TABLE `activity`
CHANGE COLUMN `status` `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '10' COMMENT '0 删除 10 草稿 20 发布' ;

ALTER TABLE `question`
CHANGE COLUMN `title` `title` VARCHAR(255) NULL DEFAULT NULL COMMENT '问题标题' ,
CHANGE COLUMN `desc` `desc` VARCHAR(255) NULL DEFAULT NULL COMMENT '问题描述' ,
CHANGE COLUMN `status` `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '10' COMMENT '10 打开 20 关闭' ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151109_060741_update_activity_status_and_question_title_desc cannot be reverted.\n";

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
