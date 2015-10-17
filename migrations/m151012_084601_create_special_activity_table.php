<?php

use yii\db\Schema;
use yii\db\Migration;

class m151012_084601_create_special_activity_table extends Migration
{
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `special_activity` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `special_id` INT(11) UNSIGNED NOT NULL COMMENT '专题ID',
  `activity_id` INT(11) UNSIGNED NOT NULL COMMENT '活动ID',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `special_id_UNIQUE` (`special_id` ASC, `activity_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = '专题活动关联表';
SQL;
        $this->execute($sql);
        return true;

    }

    public function safeDown()
    {
        $this->dropTable('special_activity');
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
