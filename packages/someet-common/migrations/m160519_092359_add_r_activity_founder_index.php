<?php

use yii\db\Migration;

class m160519_092359_add_r_activity_founder_index extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `r_activity_founder` ADD UNIQUE `founder_activity_UNIQUE` USING BTREE(`activity_id`, `founder_id`) comment '';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160519_092359_add_r_activity_founder_index cannot be reverted.\n";

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
