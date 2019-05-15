<?php

use yii\db\Migration;

class m170329_072118_remove_response_code_from_rejection extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('rejection','response_code');
    }

    public function safeDown()
    {
        $this->addColumn('rejection','response_code',$this->integer(4)->after('type')
            ->comment('Email response code in case of bounce'));
    }
}
