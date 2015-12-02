<?php

use yii\db\Schema;
use yii\db\Migration;

class m151019_045953_add_tag_frequency_field extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `activity_tag`
DROP COLUMN `label`,
ADD COLUMN `frequency` INT(11) NOT NULL DEFAULT 0 COMMENT '频率' AFTER `id`,
ADD COLUMN `name` VARCHAR(190) NOT NULL COMMENT '标签名称' AFTER `frequency`,
DROP INDEX `title_UNIQUE` ,
ADD UNIQUE INDEX `title_UNIQUE` (`name` ASC);
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151019_045953_add_tag_frequency_field cannot be reverted.\n";

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
