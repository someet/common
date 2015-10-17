<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_073009_update_answer_item_table_add_user_id_field extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer_item`
ADD COLUMN `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID, 区分用户填写的答案' AFTER `id`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151013_073009_update_answer_item_table_add_user_id_field cannot be reverted.\n";

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
