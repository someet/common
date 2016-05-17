<?php

use yii\db\Schema;
use yii\db\Migration;

class m151123_091542_add_wechat_template_send_fields_on_answer_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
CHANGE COLUMN `is_send` `is_send` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '短信是否发送成功 0 未发送 1 发送成功 2 发送失败' ,
ADD COLUMN `wechat_template_push_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '消息模板推送的时间' AFTER `status`,
ADD COLUMN `wechat_template_is_send` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '消息模板是否已经发送, 0 未发送 1 发送成功 2 发送失败' AFTER `wechat_template_push_at`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151123_091542_add_wechat_template_send_fields_on_answer_table cannot be reverted.\n";

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
