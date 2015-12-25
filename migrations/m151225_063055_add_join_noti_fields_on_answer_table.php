<?php

use yii\db\Schema;
use yii\db\Migration;

class m151225_063055_add_join_noti_fields_on_answer_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
ADD COLUMN `join_noti_is_send` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否发送参加活动提醒' AFTER `wechat_template_msg_id`,
ADD COLUMN `join_noti_send_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送参加活动提醒的时间' AFTER `join_noti_is_send`,
ADD COLUMN `join_noti_wechat_template_push_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '参加活动提醒的微信模板消息发送时间' AFTER `join_noti_send_at`,
ADD COLUMN `join_noti_wechat_template_is_send` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '消息模板是否已经发送, 0 未发送 1 发送成功 2 发送失败' AFTER `join_noti_wechat_template_push_at`,
ADD COLUMN `join_noti_wechat_template_msg_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '模板消息的MsgID, 到时候可以查询模板消息发送是否成功' AFTER `join_noti_wechat_template_is_send`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('answer', 'join_noti_is_send');
        $this->dropColumn('answer', 'join_noti_send_at');
        $this->dropColumn('answer', 'join_noti_wechat_template_push_at');
        $this->dropColumn('answer', 'join_noti_wechat_template_is_send');
        $this->dropColumn('answer', 'join_noti_wechat_template_msg_id');
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
