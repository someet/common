<?php

use yii\db\Schema;
use yii\db\Migration;

class m160516_084717_add_space_spot_id extends Migration
{
    public function up()
    {
        $sql= <<<SQL
ALTER TABLE `activity`  ADD COLUMN `space_spot_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '场地id' AFTER `join_people_count`;
SQL;
    $this->execute($sql);
    return true;

    }

    public function down()
    {
        $this->dropColumn('space_spot_id');
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


