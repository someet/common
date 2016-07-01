<?php

use yii\db\Schema;
use yii\db\Migration;

class m160628_101009_add_device_push_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `app_push` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户编号',
  `jiguang_id` VARCHAR(64) NULL DEFAULT NULL COMMENT '极光编号',
  `content` VARCHAR(255) NOT NULL COMMENT '推送内容',
  `from_type` VARCHAR(64) NOT NULL COMMENT '内容来源类型，例如活动，用户等',
  `from_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源编号，如果是活动的话，则是活动编号',
  `from_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源的状态 例如活动的通过，不通过状态',
  `is_read` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否已读 0 未读 1 已读',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '推送时间',
  `status` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态 ',
  PRIMARY KEY (`id`),
  INDEX `idx_userid` (`user_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = 'App推送';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropTable('app_push');
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
