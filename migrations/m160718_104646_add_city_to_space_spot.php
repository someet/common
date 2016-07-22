<?php

use yii\db\Migration;

class m160718_104646_add_city_to_space_spot extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `space_spot`
ADD COLUMN `city_id` INT(11) NULL DEFAULT NULL AFTER `id`,
ADD COLUMN `city` VARCHAR(60) NULL DEFAULT NULL AFTER `city_id`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('space_spot', 'city_id');
        $this->dropColumn('space_spot', 'city');
        return true;
    }
}
