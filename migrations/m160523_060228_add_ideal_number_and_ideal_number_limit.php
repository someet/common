<?php

use yii\db\Schema;
use yii\db\Migration;

class m160523_060228_add_ideal_number_and_ideal_number_limit extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        ALTER TABLE `activity` ADD COLUMN `ideal_number` int(11) UNSIGNED NOT NULL DEFAULT 10 COMMENT '理想人数' AFTER `peoples`;
        ALTER TABLE `activity` ADD COLUMN `ideal_number_limit` int(11) UNSIGNED NOT NULL DEFAULT 10 COMMENT '理想人数限制' AFTER `ideal_number`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('ideal_number');
        $this->dropColumn('ideal_number_limit');
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
