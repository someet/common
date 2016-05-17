<?php

use yii\db\Schema;
use yii\db\Migration;

class m160122_083056_add_activity_display_order_field extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
ADD COLUMN `display_order` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '排序，从小到大排序，1在前，99在后' AFTER `edit_status`;
SQL;
        $this->execute($sql);

        return true;
    }

    public function down()
    {
        $this->dropColumn('activity', 'display_order');
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
