<?php

use yii\db\Schema;
use yii\db\Migration;

class m151204_014512_update_union_id_index extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `social_account`
DROP INDEX `union_id_UNIQUE` ,
ADD UNIQUE INDEX `union_id_UNIQUE` (`union_id` ASC, `client_id` ASC),
DROP INDEX `client_id_UNIQUE` ;
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151204_014512_update_union_id_index cannot be reverted.\n";

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
