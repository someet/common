<?php

use yii\db\Schema;
use yii\db\Migration;

class m151014_081806_add_uniq_on_r_tag_activity_table extends Migration
{
    public function up()
    {
        $sql = <<<SQL
ALTER TABLE `r_tag_activity`
ADD UNIQUE INDEX `uniq_activity_tag` (`activity_id` ASC, `tag_id` ASC);
SQL;
        $this->execute($sql);
        return true;

    }

    public function down()
    {
        echo "m151014_081806_add_uniq_on_r_tag_activity_table cannot be reverted.\n";

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
