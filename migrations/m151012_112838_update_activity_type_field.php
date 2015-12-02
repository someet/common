<?php

use yii\db\Schema;
use yii\db\Migration;

class m151012_112838_update_activity_type_field extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity_type`
CHANGE COLUMN `displayorder` `display_order` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '显示顺序' ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151012_112838_update_activity_type_field cannot be reverted.\n";

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
