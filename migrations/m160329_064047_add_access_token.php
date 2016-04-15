<?php

use yii\db\Schema;
use yii\db\Migration;

class m160329_064047_add_access_token extends Migration
{
    public function up()
    {
        $sql = <<<SQL
        ALTER TABLE `user`
        ADD COLUMN `access_token` VARCHAR(32) NOT NULL COMMENT '用于移动端访问的TOKEN';
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropColumn('user', 'access_token');
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
