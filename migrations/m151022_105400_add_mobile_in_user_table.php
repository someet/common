<?php

use yii\db\Schema;
use yii\db\Migration;

class m151022_105400_add_mobile_in_user_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `user`
ADD COLUMN `mobile` VARCHAR(45) NULL DEFAULT NULL AFTER `in_white_list`,
ADD UNIQUE INDEX `mobile_UNIQUE` (`mobile` ASC)
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('user', 'mobile');
        $this->dropIndex('mobile_UNIQUE', 'user');

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
