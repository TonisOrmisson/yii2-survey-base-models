<?php

use yii\db\Migration;

class m170302_104953_create_rejections extends Migration
{
    private $tableName ='rejection';


    public function safeUp()
    {
        if (!in_array($this->tableName, $this->getDb()->schema->tableNames)) {
            $this->createTable($this->tableName, [
                'rejection_id' => $this->primaryKey()->comment('ID'),
                'survey_id' => $this->integer()->comment('Survey'),
                'respondent_id' => $this->integer()->comment('Respondent'),
                'email_address' => $this->string(255)->notNull()->comment('email address to send email to'),
                'response_code' => $this->integer(4)->comment('Email response code in case of bounce'),
                'type' => $this->string(45)->comment('Bounce type'),
                'bounce' => $this->text()->comment('Sisimai bounce object as JSON'),
                'time_rejected' => $this->dateTime()->notNull()->comment('Rejection time'),
            ]);

            $this->addForeignKey('fk_rejection_survey_id',$this->tableName,'survey_id','survey','survey_id');
            $this->addForeignKey('fk_rejection_respondent_id','rejection','respondent_id','respondent','respondent_id');
        }

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_rejection_survey_id',$this->tableName);
        $this->dropForeignKey('fk_rejection_email_id',$this->tableName);
        $this->dropForeignKey('fk_rejection_respondent_id',$this->tableName);
        $this->dropTable($this->tableName);
    }
}
