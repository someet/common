<?php

use yii\db\Schema;
use yii\db\Migration;

class m151202_091453_add_wechat_msg_id_on_answer_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
ADD COLUMN `wechat_template_msg_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '模板消息的MsgID, 到时候可以查询模板消息发送是否成功' AFTER `wechat_template_is_send`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('answer', 'wechat_template_msg_id');
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
