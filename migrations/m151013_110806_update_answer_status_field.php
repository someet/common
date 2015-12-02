<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_110806_update_answer_status_field extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
CHANGE COLUMN `status` `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '10 未审核 20 通过 30 不通过' ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151013_110806_update_answer_status_field cannot be reverted.\n";

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
