<?php

use yii\db\Schema;
use yii\db\Migration;

class m160317_120512_yellow_card_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
DROP TABLE IF EXISTS `yellow_card`;
CREATE TABLE `yellow_card` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `username` varchar(255) DEFAULT NULL COMMENT '用户名字',
  `activity_id` int(11) DEFAULT '0' COMMENT '活动id',
  `activity_title` varchar(255) DEFAULT NULL COMMENT '活动标题',
  `card_num` int(11) unsigned DEFAULT NULL COMMENT '黄牌数量 ',
  `card_category` tinyint(255) DEFAULT NULL COMMENT '分类（黄牌原因理由）1 迟到 2请假 3爽约',
  `created_at` varchar(255) DEFAULT NULL COMMENT '黄牌创建时间',
  `invalid_time` int(11) DEFAULT NULL COMMENT '黄牌失效时间',
  `appeal_reason` varchar(255) DEFAULT NULL COMMENT '申诉理由',
  `appeal_status` tinyint(11) DEFAULT NULL COMMENT '0未申诉  1申诉中  2同意取消  3驳回',
  `appeal_time` int(11) DEFAULT NULL COMMENT '申诉时间',
  `status` varchar(255) DEFAULT '0' COMMENT '0 正常使用 1取消黄牌',
  `handle_time` int(11) DEFAULT NULL COMMENT '处理时间',
  `handle_user_id` int(11) DEFAULT '0' COMMENT '处理人',
  `handle_username` varchar(255) DEFAULT NULL COMMENT '处理人名字',
  `handle_reply` varchar(255) DEFAULT NULL COMMENT '回复申诉理由',
  `handle_result` varchar(255) DEFAULT NULL COMMENT '实际处理结果 1处理完成  2驳回   0 处理中',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160317_120512_yellow_card_table cannot be reverted.\n";

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
