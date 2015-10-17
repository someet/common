<?php

use yii\db\Schema;
use yii\db\Migration;

class m151014_080739_add_activity_tag_and_relation_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `activity_tag` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(255) NOT NULL COMMENT '标签',
  `status` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '冗余扩展',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `title_UNIQUE` (`label` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = '活动标签';

CREATE TABLE IF NOT EXISTS `r_tag_activity` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id` INT(11) UNSIGNED NOT NULL COMMENT '活动ID',
  `tag_id` INT(11) UNSIGNED NOT NULL COMMENT '标签ID',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = '活动标签关联表';
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {

        $this->dropTable('activity_tag');
        $this->dropTable('r_tag_activity');
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
