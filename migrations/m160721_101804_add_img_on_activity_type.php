<?php

use yii\db\Migration;

class m160721_101804_add_img_on_activity_type extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity_type`
ADD COLUMN `img` VARCHAR(255) NULL DEFAULT NULL AFTER `status`;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropColumn('activity_type', 'img');
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
