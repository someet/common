<?php

use yii\db\Schema;
use yii\db\Migration;

class m160727_060053_set_default_value_and_add_index extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
ADD INDEX `idx_city_id` (`city_id` ASC);

ALTER TABLE `activity_type`
CHANGE COLUMN `city_id` `city_id` INT(11) NULL DEFAULT 2 ,
CHANGE COLUMN `city` `city` VARCHAR(60) NULL DEFAULT '北京' ,
ADD INDEX `idx_city_id` (`city_id` ASC);

ALTER TABLE `space_spot`
CHANGE COLUMN `city_id` `city_id` INT(11) NULL DEFAULT 2 ,
CHANGE COLUMN `city` `city` VARCHAR(60) NULL DEFAULT '北京' ,
ADD INDEX `idx_city_id` (`city_id` ASC);

ALTER TABLE `space_type`
CHANGE COLUMN `city_id` `city_id` INT(11) NULL DEFAULT 2 ,
CHANGE COLUMN `city` `city` VARCHAR(60) NULL DEFAULT '北京' ,
ADD INDEX `idx_city_id` (`city_id` ASC);
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m160727_060053_set_default_value_and_add_index cannot be reverted.\n";

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
