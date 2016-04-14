<?php

use yii\db\Schema;
use yii\db\Migration;

class m160414_021056_add_arrive_status_comment extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
CHANGE COLUMN `arrive_status` `arrive_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '到达状态 0未到 1迟到 2准时 10 未设置' ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m160414_021056_add_arrive_status_comment cannot be reverted.\n";

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
