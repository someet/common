<?php

use yii\db\Migration;

class m160503_074314_add_space extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `space_section` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(180) NOT NULL COMMENT '空间名称',
  `spot_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '地点编号',
  `people` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '推荐人数',
  `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = '空间区域';
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropTable('space');
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
