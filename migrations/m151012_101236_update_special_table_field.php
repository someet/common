<?php

use yii\db\Schema;
use yii\db\Migration;

class m151012_101236_update_special_table_field extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `special`
CHANGE COLUMN `displayorder` `display_order` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '显示排序' ,
CHANGE COLUMN `sharetimes` `share_times` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分享次数' ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151012_101236_update_special_table_field cannot be reverted.\n";

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
