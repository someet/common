<?php

use yii\db\Schema;
use yii\db\Migration;

class m160129_103956_uga_question_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `uga_question` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `content` VARCHAR(190) NOT NULL DEFAULT 0 COMMENT '问题内容',
  `is_official` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否为官方 0 民间  1 官方',
  `praise_num` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '点赞的总数量',
  `anwers_num` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '问题回答的总数量',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '问题创建时间  时间戳格式',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0删除  1正常 发布',
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = 'UGa system question table';

CREATE TABLE IF NOT EXISTS `uga_answer` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户 id',
  `question_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '问题id',
  `content` VARCHAR(190) NOT NULL DEFAULT 0 COMMENT '回答内容   ',
  `praise` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '点赞数量',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0删除1正常',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uniq_user` (`user_id` ASC, `question_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = 'uga  answer  tables';

CREATE TABLE IF NOT EXISTS `uga_praise` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `answer_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '回答的id',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户的id',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uniq_user` (`answer_id` ASC, `user_id` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = 'uga praise table';
SQL;
         $this->execute($sql);
                return true;
    }

    public function down()
    {
        $this->dropTable('{{%uga_question}}');
        $this->dropTable('{{%uga_answer}}');
        $this->dropTable('{{%uga_praise}}');
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
