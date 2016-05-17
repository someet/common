<?php

use yii\db\Schema;
use yii\db\Migration;

class m151022_093645_update_user_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        ALTER TABLE `user`
ADD COLUMN `allow_join_times` TINYINT(3) NOT NULL DEFAULT 5 COMMENT '一周允许参加活动的次数' AFTER `flags`,
ADD COLUMN `punish_score` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '惩罚的分数' AFTER `allow_join_times`,
ADD COLUMN `in_white_list` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否在白名单中' AFTER `punish_score`,
ADD COLUMN `status` SMALLINT(6) NOT NULL DEFAULT 10 COMMENT '冗余扩展' AFTER `in_white_list`
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('user', 'allow_join_times');
        $this->dropColumn('user', 'punish_score');
        $this->dropColumn('user', 'in_white_list');
        $this->dropColumn('user', 'status');
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
