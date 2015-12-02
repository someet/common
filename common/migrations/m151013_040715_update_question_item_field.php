<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_040715_update_question_item_field extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `question_item`
CHANGE COLUMN `listorder` `display_order` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '问题项排序' ;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m151013_040715_update_question_item_field cannot be reverted.\n";

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
