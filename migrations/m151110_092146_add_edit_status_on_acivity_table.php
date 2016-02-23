<?php

use yii\db\Schema;
use yii\db\Migration;

class m151110_092146_add_edit_status_on_acivity_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
ADD COLUMN `edit_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '扩展字段, 前端自定义状态' AFTER `status`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('activity', 'edit_status');

        return true;
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
