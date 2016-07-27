<?php

use yii\db\Schema;
use yii\db\Migration;

class m160705_105227_add_app_push extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        DROP TABLE IF EXISTS `app_push`;
        CREATE TABLE `app_push` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户编号',
          `jiguang_id` varchar(64) DEFAULT '0' COMMENT '极光编号',
          `content` varchar(255) NOT NULL COMMENT '推送内容',
          `from_type` varchar(64) NOT NULL COMMENT '内容来源类型，例如活动，用户等',
          `from_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '来源编号，如果是活动的话，则是活动编号',
          `from_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '来源的状态 例如活动的通过，不通过状态',
          `is_join_queue` int(11) NOT NULL DEFAULT '0' COMMENT '是否加入队列 0 未加入 1加入',
          `join_at` int(11) DEFAULT '0' COMMENT '加入队列时间',
          `is_push` int(11) DEFAULT '0' COMMENT '是否推送 0未推送 1推送',
          `is_read` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读 0 未读 1 已读',
          `push_at` int(11) DEFAULT '0' COMMENT '推送时间',
          `created_at` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
          `status` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '状态 ',
          PRIMARY KEY (`id`),
          KEY `idx_userid` (`user_id`)
        )  COMMENT='App推送';

SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropTable('app_push');
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
