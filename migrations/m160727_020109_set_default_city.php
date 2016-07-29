<?php

use yii\db\Schema;
use yii\db\Migration;

class m160727_020109_set_default_city extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
CHANGE COLUMN `city_id` `city_id` INT(11) NULL DEFAULT 2 ,
CHANGE COLUMN `city` `city` VARCHAR(60) NULL DEFAULT '北京' ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m160727_020109_set_default_city cannot be reverted.\n";

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
