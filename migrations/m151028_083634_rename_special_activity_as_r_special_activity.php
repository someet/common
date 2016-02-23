<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_083634_rename_special_activity_as_r_special_activity extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `special_activity`
RENAME TO  `r_special_activity` ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151028_083634_rename_special_activity_as_r_special_activity cannot be reverted.\n";

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
