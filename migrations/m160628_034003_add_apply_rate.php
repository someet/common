<?php

use yii\db\Migration;

class m160628_034003_add_apply_rate extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity` ADD COLUMN `apply_rate` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '报名率' AFTER `content`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('activity', 'content');
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
