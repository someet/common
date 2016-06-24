<?php

use yii\db\Migration;

class m160624_075609_add_table_mobile_msg extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE `mobile_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `username` varchar(255) DEFAULT NULL COMMENT '用户名',
  `mobile_num` varchar(45) NOT NULL DEFAULT '0' COMMENT '手机号',
  `mobile_ model` int(11) DEFAULT NULL COMMENT '手机型号',
  `activity_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动id',
  `content` text COMMENT '短信内容',
  `is_join_queue` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否加入队列 1加入 0未加入',
  `join_queue_at` int(11) DEFAULT NULL COMMENT '队列加入时间',
  `is_send` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '是否发送 1发送 0未发送',
  `send_at` int(11) DEFAULT NULL COMMENT '信息发送时间',
  `create_at` int(11) NOT NULL DEFAULT '0' COMMENT '事件发生时间创建时间',
  `msg_type` int(11) DEFAULT NULL COMMENT '短信类型',
  `status` varchar(255) DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
);
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropTable('mobile_msg');
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
