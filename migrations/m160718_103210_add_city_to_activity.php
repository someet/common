<?php

use yii\db\Migration;

class m160718_103210_add_city_to_activity extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity`
ADD COLUMN `city_id` INT(11) NULL DEFAULT NULL AFTER `id`,
ADD COLUMN `city` VARCHAR(60) NULL DEFAULT NULL AFTER `city_id`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('activity', 'city_id');
        $this->dropColumn('activity', 'city');
        return true;
    }
}
