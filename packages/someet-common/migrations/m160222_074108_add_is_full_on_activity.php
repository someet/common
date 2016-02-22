<?php

use yii\db\Schema;
use yii\db\Migration;

class m160222_074108_add_is_full_on_activity extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
ADD COLUMN `is_full` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已报满 0 未报满 1 已报满' AFTER `co_founder4`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('activity', 'is_full');
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
