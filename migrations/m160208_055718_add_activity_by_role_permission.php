<?php

use yii\db\Schema;
use yii\db\Migration;

class m160208_055718_add_activity_by_role_permission extends Migration
{
    public function up()
    {
        $items = [
            '/mobile/member/activity-by-role' => ['founder'],
            '/mobile/member/activity-by-role' => ['pma'],
        ];

        $authItemTemplate = <<<SQL
INSERT INTO auth_item (name, type, description, rule_name, data, created_at, updated_at) VALUES ('%s', '2', '', null, null, null, null);
SQL;
        $itemChildTemplate = <<<SQL
        INSERT INTO auth_item_child (parent, child) VALUES ('%s', '%s');
SQL;
        $sql = '';
        foreach ($items as $item => $roles) {
            $sql .= sprintf($authItemTemplate, $item);
            foreach ($roles as $role) {
                $sql .= sprintf($itemChildTemplate, $role, $item);
            }
        }
        $this->execute($sql);
        return true;
    }

    public function down()
    {
        echo "m160208_055718_add_activity_by_role_permission cannot be reverted.\n";

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
<?php

use yii\db\Schema;
use yii\db\Migration;

class m160217_023615_fix_founder_activity_by_role extends Migration
{
    public function up()
    {

        $this->update('auth_item_child',['parent' => 'founder'],'child = "/mobile/member/activity-by-role" ');

        return true;
    }

    public function down()
    {
        echo "m160217_023615_fix_founder_activity_by_role cannot be reverted.\n";

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

