<?php

use yii\db\Schema;
use yii\db\Migration;

class m160512_032653_add_r_activity_space_table extends Migration
{
    public function up()
    {
        $sql= <<<SQL
CREATE TABLE `r_activity_space` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `activity_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动id',
  `space_spot_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '场地id',
  `space_section_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '空间id',
  PRIMARY KEY (`id`)
);
SQL;
    $this->execute($sql);
    return true;

    }

    public function down()
    {
        $this->dropTable('r_activity_space');
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


