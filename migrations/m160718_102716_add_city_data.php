<?php

use yii\db\Migration;

class m160718_102716_add_city_data extends Migration
{
    public function up()
    {
        $sql = <<<SQL
INSERT INTO `city` (`city`, `city_id`, `status`) VALUES ('全国', '1', '1');
INSERT INTO `city` (`city`, `city_id`, `status`) VALUES ('北京', '2', '1');
INSERT INTO `city` (`city`, `city_id`, `status`) VALUES ('上海', '3', '1');
INSERT INTO `city` (`city`, `city_id`, `status`) VALUES ('广州', '4', '1');
INSERT INTO `city` (`city`, `city_id`, `status`) VALUES ('深圳', '5', '1');
SQL;
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160718_102716_add_city_data cannot be reverted.\n";

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
