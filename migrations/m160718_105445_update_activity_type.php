<?php

use yii\db\Migration;

class m160718_105445_update_activity_type extends Migration
{
    public function up()
    {
        $sql = <<<SQL
UPDATE `activity_type` SET `city_id`='2', `city`='北京' WHERE `id`>0;
UPDATE `activity` SET `city_id`='2', `city`='北京' WHERE `id`>0;
UPDATE `space_type` SET `city_id`='2', `city`='北京' WHERE `id`>0;
UPDATE `space_spot` SET `city_id`='2', `city`='北京' WHERE `id`>0;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m160718_105445_update_activity_type cannot be reverted.\n";

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
