<?php

use yii\db\Schema;
use yii\db\Migration;

class m160322_095211_add_noti_tables extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `noti_tunnel` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL COMMENT '渠道名称',
  `mark` VARCHAR(180) NULL DEFAULT NULL COMMENT '渠道备注',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '渠道状态 10 可用 0 不可用',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '通知的渠道表';

CREATE TABLE IF NOT EXISTS `noti_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL COMMENT '通知类型的名称',
  `mark` VARCHAR(180) NULL DEFAULT NULL COMMENT '活动分类的备注',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '状态 10 可用 0 不可用',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '通知的类型表';

CREATE TABLE IF NOT EXISTS `noti_template` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL COMMENT '通知模板的名称',
  `template` VARCHAR(180) NOT NULL COMMENT '通知模板的内容',
  `type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '通知模板的分类ID',
  `mark` VARCHAR(180) NULL DEFAULT NULL COMMENT '通知模板的备注',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 10 COMMENT '通知模板的状态 10 可用 0 不可用',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '通知模板表';

CREATE TABLE IF NOT EXISTS `noti` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tunnel_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '渠道ID',
  `type_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `new` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为新通知 1 未读 0 已读',
  `author` VARCHAR(60) NOT NULL COMMENT '通知产生者',
  `author_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '通知产生者ID',
  `note` VARCHAR(180) NOT NULL COMMENT '通知内容',
  `from_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源ID',
  `from_id_type` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源ID的类型，例如是活动',
  `from_num` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '通知来源的相同数量',
  `sended_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '通知发送时间',
  `callback_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '通知发送后返回的通知ID',
  `callback_msg` VARCHAR(180) NULL DEFAULT NULL COMMENT '通知发送后返回的消息是成功或失败的原因',
  `callback_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '通知发送后的状态，10 成功 20 失败',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '通知产生的时间',
  `timing` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '定时',
  `work_on` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '可以发送通知的开始时间',
  `work_off` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '可以发送通知的结束时间',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '通知表';

CREATE TABLE IF NOT EXISTS `noti_from_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) NOT NULL COMMENT '通知来源的类型名称',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '通知来源类型表';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropTable('noti_from_type');
        $this->dropTable('noti');
        $this->dropTable('noti_template');
        $this->dropTable('noti_type');
        $this->dropTable('noti_tunnel');
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
