<?php

use yii\db\Migration;

class m170327_130608_rename_rejection_type extends Migration
{
    public function safeUp()
    {
        $this->renameColumn('rejection','bounce_type','type');
    }

    public function safeDown()
    {
        $this->renameColumn('rejection','type','bounce_type');
    }
}
