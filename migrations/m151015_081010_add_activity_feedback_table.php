<?php

use yii\db\Schema;
use yii\db\Migration;

class m151015_081010_add_activity_feedback_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `activity_feedback` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `activity_id` INT(11) UNSIGNED NOT NULL COMMENT '活动ID',
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT '用户ID',
  `stars` INT(10) UNSIGNED NOT NULL COMMENT '评分, 几星',
  `feedback` VARCHAR(255) NOT NULL COMMENT '反馈内容',
  `created_at` INT(11) UNSIGNED NOT NULL COMMENT '反馈时间',
  `updated_at` INT(11) UNSIGNED NOT NULL COMMENT '处理时间',
  `status` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '冗余扩展',
  PRIMARY KEY (`id`))
COMMENT = '活动反馈表';
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        $this->dropTable('activity_feedback');
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
