<?php

use yii\db\Schema;
use yii\db\Migration;

class m151001_103523_create_activity_type_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `activity_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT '分类名称',
  `displayorder` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '显示顺序',
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '冗余扩展',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = '活动类型表';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropTable('activity_type');
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
