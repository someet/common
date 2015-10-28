<?php

use yii\db\Schema;
use yii\db\Migration;

class m151027_095123_update_username_and_email_fields extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `user`
CHANGE COLUMN `username` `username` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT '用户名, 允许登录' ,
CHANGE COLUMN `email` `email` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_unicode_ci' NULL DEFAULT NULL COMMENT '邮箱, 允许登录'
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151027_095123_update_username_and_email_fields cannot be reverted.\n";

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
