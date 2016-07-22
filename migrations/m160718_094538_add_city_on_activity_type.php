<?php

use yii\db\Migration;

class m160718_094538_add_city_on_activity_type extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity_type`
ADD COLUMN `city_id` INT(11) NULL DEFAULT NULL AFTER `id`,
ADD COLUMN `city` VARCHAR(60) NULL DEFAULT NULL AFTER `city_id`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('activity_type', 'city_id');
        $this->dropColumn('activity_type', 'city');
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
