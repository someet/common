<?php

use yii\db\Migration;

class m160718_082742_add_city extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `city` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `city` VARCHAR(60) NOT NULL COMMENT '城市',
  `city_id` INT(11) UNSIGNED NOT NULL COMMENT '城市编号',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 开启 1  停用 0',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `unq_city` (`city` ASC),
  UNIQUE INDEX `unq_city_id` (`city_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        $this->dropTable('city');
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
