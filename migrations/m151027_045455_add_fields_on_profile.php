<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_045455_add_fields_on_profile extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `profile`
CHANGE COLUMN `bio` `bio` TEXT NULL DEFAULT NULL COMMENT '自我介绍' ,
ADD COLUMN `country` VARCHAR(255) NULL DEFAULT NULL COMMENT '国家' AFTER `bio`,
ADD COLUMN `province` VARCHAR(255) NULL DEFAULT NULL COMMENT '省份' AFTER `country`,
ADD COLUMN `city` VARCHAR(255) NULL DEFAULT NULL COMMENT '城市' AFTER `province`,
ADD COLUMN `headimgurl` VARCHAR(255) NULL DEFAULT NULL COMMENT '头像地址' AFTER `city`,
ADD COLUMN `sex` TINYINT(1) NULL DEFAULT 0 COMMENT '性别' AFTER `headimgurl`,
ADD COLUMN `birth_year` SMALLINT(6) NULL DEFAULT NULL AFTER `sex`,
ADD COLUMN `birth_month` TINYINT(4) NULL DEFAULT NULL AFTER `birth_year`,
ADD COLUMN `birth_day` TINYINT(4) NULL DEFAULT NULL AFTER `birth_month`,
ADD COLUMN `constellation` VARCHAR(255) NULL DEFAULT NULL COMMENT '星座, 根据生日自动生成' AFTER `birth_day`,
ADD COLUMN `zodiac` VARCHAR(255) NULL DEFAULT NULL AFTER `constellation`,
ADD COLUMN `company` VARCHAR(255) NULL DEFAULT NULL COMMENT '公司' AFTER `zodiac`,
ADD COLUMN `education` VARCHAR(255) NULL DEFAULT NULL COMMENT '学历' AFTER `company`,
ADD COLUMN `occupation` VARCHAR(255) NULL DEFAULT NULL COMMENT '职业' AFTER `education`,
ADD COLUMN `position` VARCHAR(255) NULL DEFAULT NULL COMMENT '职位' AFTER `occupation`,
ADD COLUMN `affective_status` VARCHAR(255) NULL DEFAULT NULL COMMENT '情感状态' AFTER `position`,
ADD COLUMN `lookingfor` VARCHAR(255) NULL DEFAULT NULL COMMENT '交友目的' AFTER `affective_status`,
ADD COLUMN `blood_type` VARCHAR(255) NULL DEFAULT NULL COMMENT '血型' AFTER `lookingfor`,
ADD COLUMN `height` VARCHAR(255) NULL DEFAULT NULL COMMENT '身高' AFTER `blood_type`,
ADD COLUMN `weight` VARCHAR(255) NULL DEFAULT NULL COMMENT '体重' AFTER `height`,
ADD COLUMN `interest` VARCHAR(255) NULL DEFAULT NULL COMMENT '兴趣爱好' AFTER `weight`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('profile', 'country');
        $this->dropColumn('profile', 'province');
        $this->dropColumn('profile', 'city');
        $this->dropColumn('profile', 'headimgurl');
        $this->dropColumn('profile', 'sex');
        $this->dropColumn('profile', 'birth_year');
        $this->dropColumn('profile', 'birth_month');
        $this->dropColumn('profile', 'birth_day');
        $this->dropColumn('profile', 'constellation');
        $this->dropColumn('profile', 'zodiac');
        $this->dropColumn('profile', 'company');
        $this->dropColumn('profile', 'education');
        $this->dropColumn('profile', 'occupation');
        $this->dropColumn('profile', 'position');
        $this->dropColumn('profile', 'affective_status');
        $this->dropColumn('profile', 'lookingfor');
        $this->dropColumn('profile', 'blood_type');
        $this->dropColumn('profile', 'height');
        $this->dropColumn('profile', 'weight');
        $this->dropColumn('profile', 'interest');

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
