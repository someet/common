<?php

use yii\db\Schema;
use yii\db\Migration;

class m151116_043012_add_user_profile_fields_and_drop_username_unique_index extends Migration
{
    public function up()
    {
        $sql = <<<SQL
            ALTER TABLE `activity`
            CHANGE COLUMN `edit_status` `edit_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '扩展字段, 前端自定义状态' ;

            ALTER TABLE `activity_feedback`
            CHANGE COLUMN `stars` `stars` TINYINT(3) UNSIGNED NOT NULL COMMENT '评分, 几星' ;

            ALTER TABLE `profile`
            ADD COLUMN `from` VARCHAR(255) NULL DEFAULT 0 COMMENT '1 朋友圈\n2 朋友推荐\n3 耳闻, 自己找到的' AFTER `interest`,
            ADD COLUMN `want` VARCHAR(255) NULL DEFAULT 0 COMMENT '1 独特体验拉轰活动\n2 志同道合\n3 生活技能和硬知识\n4 聊天谈理想\n5 融入兴趣圈子' AFTER `from`,
            ADD COLUMN `recommand` TEXT NULL DEFAULT NULL COMMENT '推荐一个身边有趣的人' AFTER `want`;

            ALTER TABLE `user`
            CHANGE COLUMN `username` `username` VARCHAR(255) NULL DEFAULT NULL COMMENT '昵称' ,
            DROP INDEX `user_unique_username` ;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m151116_043012_add_user_profile_fields_and_drop_username_unique_index cannot be reverted.\n";

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
