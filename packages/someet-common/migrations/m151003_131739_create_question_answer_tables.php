<?php

use yii\db\Schema;
use yii\db\Migration;

class m151003_131739_create_question_answer_tables extends Migration
{
    public function safeUp()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `question` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id` INT(11) UNSIGNED NOT NULL COMMENT '活动ID',
  `title` VARCHAR(255) NOT NULL COMMENT '问题标题',
  `desc` VARCHAR(255) NOT NULL COMMENT '问题描述',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0 删除 10 草稿 20 正常',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uniq_activity` (`activity_id` ASC))
COMMENT = '问题表';

CREATE TABLE IF NOT EXISTS `question_item` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` INT(11) UNSIGNED NOT NULL COMMENT '问题ID',
  `type_id` INT(11) UNSIGNED NOT NULL COMMENT '类型ID',
  `label` VARCHAR(255) NOT NULL COMMENT '标题',
  `desc` VARCHAR(255) NOT NULL COMMENT '简介',
  `extra` VARCHAR(255) NULL DEFAULT NULL COMMENT '多选框, 下拉菜单这种字段的 多个值, 以分号分割的字符串信息',
  `listorder` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '问题项排序',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '冗余扩展',
  PRIMARY KEY (`id`))
COMMENT = '问题项表';

CREATE TABLE IF NOT EXISTS `question_type` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `field_name` VARCHAR(60) NOT NULL COMMENT '字段名称',
  `field_lib` VARCHAR(60) NOT NULL COMMENT 'html模板信息',
  `field_type` TINYINT(3) UNSIGNED NOT NULL COMMENT '可用的全部字段类型 (输入框, 文本域, 下拉菜单, 上传框等)\ntext, number, textarea, dropdownlist, radio, checkbox, date, file, img',
  `check_type` TINYINT(3) UNSIGNED NOT NULL COMMENT '标识 该字段类型 用什么方式验证, 比如 数字, 邮箱, 字符串, 单选, 多选, 文件上传. 用于表单的字段验证',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '冗余扩展',
  PRIMARY KEY (`id`))
COMMENT = '问题类型表';

CREATE TABLE IF NOT EXISTS `answer` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_id` INT(11) UNSIGNED NOT NULL COMMENT '问题ID',
  `activity_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '活动ID',
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID',
  `is_finish` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 进行中 1 已完成',
  `created_at` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0 删除 10 正常',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uniq_form_created_by` (`question_id` ASC))
COMMENT = '答案表';

CREATE TABLE IF NOT EXISTS `answer_item` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_item_id` INT(11) UNSIGNED NOT NULL COMMENT '问题项ID',
  `question_id` INT(11) UNSIGNED NOT NULL COMMENT '问题项ID',
  `question_label` VARCHAR(255) NOT NULL COMMENT '问题标题, 为了保留历史记录',
  `question_value` VARCHAR(255) NOT NULL COMMENT '问题的值',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '冗余扩展',
  PRIMARY KEY (`id`))
COMMENT = '答案项表';
SQL;
        $this->execute($sql);
        return true;
    }

    public function safeDown()
    {
        $this->dropTable('question');
        $this->dropTable('question_item');
        $this->dropTable('answer');
        $this->dropTable('answer_item');
        $this->dropTable('question_type');
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
