<?php

use yii\db\Schema;
use yii\db\Migration;

class m160329_060551_add_yellow_card_category_comment extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `yellow_card` CHANGE COLUMN `card_category` `card_category` tinyint(255) DEFAULT NULL COMMENT '选择类别（黄牌原因理由）1 迟到 2请假1  3请假2 4爽约 5带人 6骚扰';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160329_060551_add_yellow_card_category_comment cannot be reverted.\n";

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
