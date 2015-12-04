<?php

use yii\db\Schema;
use yii\db\Migration;

class m151204_023858_update_union_id_field_and_add_unionid_on_user_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `social_account`
CHANGE COLUMN `union_id` `unionid` VARCHAR(60) NULL DEFAULT NULL ;

ALTER TABLE `user`
ADD COLUMN `unionid` VARCHAR(60) NULL DEFAULT NULL COMMENT '现在只考虑微信登录的                   union_id' AFTER `activities_count`;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151204_023858_update_union_id_field_and_add_unionid_on_user_table cannot be reverted.\n";

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
