<?php

use yii\db\Schema;
use yii\db\Migration;

class m160324_071216_add_check_in_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `activity_check_in` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '活动ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `username` VARCHAR(60) NOT NULL COMMENT '用户名',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '签到时间',
  `longitude` DOUBLE UNSIGNED NOT NULL DEFAULT 0 COMMENT '经度',
  `latitude` DOUBLE UNSIGNED NOT NULL DEFAULT 0 COMMENT '纬度',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '签到状态 1 签到 0 未签到',
  `mark` VARCHAR(60) NOT NULL COMMENT '签到备注',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '签到';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropTable('activity_check_in');
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
