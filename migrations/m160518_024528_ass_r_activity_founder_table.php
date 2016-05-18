<?php

use yii\db\Migration;

class m160518_024528_ass_r_activity_founder_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
CREATE TABLE `r_activity_founder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `activity_id` int(11) unsigned DEFAULT '0' COMMENT '活动id',
  `founder_id` int(11) unsigned DEFAULT '0' COMMENT '发起人id',
  PRIMARY KEY (`id`)
) COMMENT='活动与发起人列表';
SQL;
    }

    public function down()
    {
        $this->dropColumn('space_spot_id');
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
