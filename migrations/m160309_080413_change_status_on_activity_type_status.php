<?php

use yii\db\Schema;
use yii\db\Migration;

class m160309_080413_change_status_on_activity_type_status extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity_type`
CHANGE COLUMN `status` `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '10' COMMENT '0 删除 10 正常 20 隐藏' ;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160309_080413_change_status_on_activity_type_status cannot be reverted.\n";

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
