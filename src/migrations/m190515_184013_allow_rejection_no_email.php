<?php

use yii\db\Migration;

/**
 * Class m190515_184013_allow_rejection_no_email
 */
class m190515_184013_allow_rejection_no_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('rejection', 'email_address', $this->string(255)->null()->comment('email address to send email to'));

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('rejection', 'email_address', $this->string(255)->notNull()->comment('email address to send email to'));
    }
}
