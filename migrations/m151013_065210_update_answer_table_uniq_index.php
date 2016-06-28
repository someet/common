<?php

use yii\db\Schema;
use yii\db\Migration;

class m151013_065210_update_answer_table_uniq_index extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `answer`
DROP INDEX `uniq_form_created_by` ,
ADD UNIQUE INDEX `uniq_form_created_by` (`question_id` ASC, `user_id` ASC);
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151013_065210_update_answer_table_uniq_index cannot be reverted.\n";

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
