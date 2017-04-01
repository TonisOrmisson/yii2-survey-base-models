<?php

use yii\db\Migration;

class m170401_163726_add_phone_numbers_to_respondent extends Migration
{
    public function safeUp()
    {
        $this->addColumn('respondent','phone_number',$this->string(64)->null()->after('alternative_email_addresses')->comment('Phone number'));
        $this->addColumn('respondent','alternative_phone_numbers',$this->text()->null()->after('phone_number')->comment('Alternative phone numbers'));
    }

    public function safeDown()
    {
        $this->dropColumn('respondent','phone_number');
        $this->dropColumn('respondent','alternative_phone_numbers');
    }
}
