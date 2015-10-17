<?php

use yii\db\Schema;
use yii\db\Migration;

class m151015_073146_add_sms_template_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `sms_template` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL COMMENT '模板名称, 例如, 审核通过, 审核失败, 请假通知',
  `template` VARCHAR(255) NOT NULL,
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '冗余扩展',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci
COMMENT = '消息模板';
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropTable('sms_template');

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
