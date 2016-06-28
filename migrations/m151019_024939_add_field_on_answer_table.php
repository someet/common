<?php

use yii\db\Schema;
use yii\db\Migration;

class m151019_024939_add_field_on_answer_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
ADD COLUMN `is_send` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已经发送审核结果给用户' AFTER `is_finish`;
SQL;
       $this->execute($sql);
       return true;
    }

    public function down()
    {
        $this->dropColumn('answer', 'is_send');

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
