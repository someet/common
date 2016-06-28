<?php

use yii\db\Schema;
use yii\db\Migration;

class m151204_024810_add_uniq_index_on_unionid_field extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `user`
ADD UNIQUE INDEX `uniq_unionid` (`unionid` ASC);
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m151204_024810_add_uniq_index_on_unionid_field cannot be reverted.\n";

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
