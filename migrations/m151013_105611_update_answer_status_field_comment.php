<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_105611_update_answer_status_field_comment extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
CHANGE COLUMN `status` `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0 未审核 10 通过 20 不通过' ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151013_105611_update_answer_status_field_comment cannot be reverted.\n";

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
