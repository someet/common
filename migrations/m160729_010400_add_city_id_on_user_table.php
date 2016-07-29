<?php

use yii\db\Schema;
use yii\db\Migration;

class m160729_010400_add_city_id_on_user_table extends Migration
{
    public function safeUp()
    {
        $sql = <<<SQL
ALTER TABLE `user`
ADD COLUMN `city_id` INT(11) NULL DEFAULT 2 COMMENT '城市编号，默认为2 北京' AFTER `subscribe_time`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function safeDown()
    {
        $this->dropColumn('user', 'city_id');
        return true;
    }

}
