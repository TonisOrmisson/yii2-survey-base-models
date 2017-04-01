<?php

use yii\db\Migration;

class m170330_110303_add_respondent_alternative_emails extends Migration
{
    public function safeUp()
    {
        $this->addColumn('respondent','alternative_email_addresses',$this->text()
            ->null()
            ->after('email_address')
            ->comment('Alternative email addresses'));
    }

    public function safeDown()
    {
        $this->dropColumn('respondent','alternative_email_addresses');
    }
}
