<?php

use yii\db\Schema;
use yii\db\Migration;

class m160222_081819_add_join_people_count extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
ADD COLUMN `join_people_count` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '已报名人数' AFTER `is_full`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {

        $this->dropColumn('activity', 'join_people_count');
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
