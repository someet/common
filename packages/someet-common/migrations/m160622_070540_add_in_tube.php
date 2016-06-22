<?php

use yii\db\Migration;

class m160622_070540_add_in_tube extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `noti`
ADD COLUMN `in_tube` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 未加入队列 1 加入队列成功 2 加入队列失败' AFTER `work_off`,
ADD COLUMN `in_tube_time` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '加入队列的时间' AFTER `in_tube`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('noti', 'in_tube');
        $this->dropColumn('noti', 'in_tube_time');
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
