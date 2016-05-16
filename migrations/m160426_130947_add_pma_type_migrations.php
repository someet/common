<?php

use yii\db\Schema;
use yii\db\Migration;

class m160426_130947_add_pma_type_migrations extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity` ADD COLUMN `pma_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是线上pma 或线下pma 0 线上 1 线下' AFTER `principal`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m160426_130947_add_pma_type_migrations cannot be reverted.\n";

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
