<?php

use yii\db\Schema;
use yii\db\Migration;

class m151113_020853_add_uniq_index_on_activity_feedback extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity_feedback`
ADD UNIQUE INDEX `uniq_user_activity` (`activity_id` ASC, `user_id` ASC)
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropIndex('uniq_user_activity', 'activity_feedback');
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
