<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_050214_update_question_status extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `question`
CHANGE COLUMN `status` `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '0 关闭 10 草稿 20 正常' ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151013_050214_update_question_status cannot be reverted.\n";

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
