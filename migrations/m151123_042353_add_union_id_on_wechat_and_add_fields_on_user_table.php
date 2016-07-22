<?php

use yii\db\Schema;
use yii\db\Migration;

class m151123_042353_add_union_id_on_wechat_and_add_fields_on_user_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `social_account`
CHANGE COLUMN `client_id` `client_id` VARCHAR(190) NULL DEFAULT NULL ,
ADD COLUMN `union_id` VARCHAR(60) NULL DEFAULT NULL AFTER `username`,
ADD UNIQUE INDEX `union_id_UNIQUE` (`union_id` ASC),
ADD UNIQUE INDEX `client_id_UNIQUE` (`client_id` ASC);

ALTER TABLE `user`
CHANGE COLUMN `password_hash` `password_hash` VARCHAR(60) NOT NULL COMMENT '密码hash' ,
CHANGE COLUMN `confirmed_at` `confirmed_at` INT(11) NULL DEFAULT NULL COMMENT '确认时间' ,
CHANGE COLUMN `unconfirmed_email` `unconfirmed_email` VARCHAR(190) NULL DEFAULT NULL COMMENT '未确认的邮箱' ,
CHANGE COLUMN `blocked_at` `blocked_at` INT(11) NULL DEFAULT NULL COMMENT '被锁定的时间' ,
CHANGE COLUMN `registration_ip` `registration_ip` VARCHAR(45) NULL DEFAULT NULL COMMENT '注册ip' ,
CHANGE COLUMN `created_at` `created_at` INT(11) NOT NULL COMMENT '注册时间' ,
CHANGE COLUMN `updated_at` `updated_at` INT(11) NOT NULL COMMENT '更新时间' ,
CHANGE COLUMN `mobile` `mobile` VARCHAR(45) NULL DEFAULT NULL COMMENT '手机号码, 允许登录' ,
ADD COLUMN `last_active_at` INT(11) NULL DEFAULT NULL COMMENT '最后一次活跃时间' AFTER `last_login_at`,
ADD COLUMN `email_confirmation_token` VARCHAR(60) NULL DEFAULT NULL COMMENT '确认邮箱token' AFTER `last_active_at`,
ADD COLUMN `password_reset_token` VARCHAR(60) NULL DEFAULT NULL COMMENT '重置密码token' AFTER `email_confirmation_token`,
ADD COLUMN `is_email_verified` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '邮箱已确认' AFTER `password_reset_token`,
ADD COLUMN `join_count` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '活动报名次数' AFTER `is_email_verified`,
ADD COLUMN `attend_count` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '活动报名通过次数, 报名被拒次数等于总数-通过次数' AFTER `join_count`,
ADD COLUMN `reject_count` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '被拒绝的次数' AFTER `attend_count`,
ADD COLUMN `activities_count` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发起的活动数量' AFTER `reject_count`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151123_042353_add_union_id_on_wechat_and_add_fields_on_user_table cannot be reverted.\n";

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
