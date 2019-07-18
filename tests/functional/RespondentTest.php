<?php

namespace andmemasin\surveybasemodels\tests\functional;


use andmemasin\surveybasemodels\Rejection;
use andmemasin\surveybasemodels\Respondent;
use Codeception\Test\Unit;


class RespondentTest extends Unit
{
    public function testValidatePhoneSurveyDuplicate() {
        $model = Respondent::findOne(1);
        $model->validate();
        $this->assertEmpty($model->errors);
    }

    public function testValidateEmailSurveyDuplicateFails() {
        $existingModel = Respondent::findOne(1);
        $existingModel->token = "new-token";
        $existingModel->respondent_id = 200;
        $model = new Respondent($existingModel->attributes);
        $model->validate('email_address');
        $this->assertContains("Duplicates some other address", $model->getFirstError('email_address'));
        $this->assertNotEmpty($model->errors);
    }

    public function testValidatePhoneSurveyDuplicateFails() {
        $existingModel = Respondent::findOne(1);
        $existingModel->token = "new-token";
        $existingModel->respondent_id = 200;
        $model = new Respondent($existingModel->attributes);
        $model->validate('phone_number');
        $this->assertContains("Duplicate phone number", $model->getFirstError('phone_number'));
        $this->assertNotEmpty($model->errors);
    }


    public function testGetIsRejected() {
        $model = Respondent::findOne(1);
        $this->assertTrue($model->isRejected);
    }

    public function testGetIsRejectedByEmail() {
        $model = Respondent::findOne(2);
        $this->assertTrue($model->isRejected);
    }

    public function testGetIsRejectedByEmailNotRejected() {
        $model = Respondent::findOne(3);
        $this->assertFalse($model->isRejected);
    }

    public function testGetLatestByEmail() {
        $result = Respondent::getLatestByEmail("rejected@example.com");
        $this->assertEquals(2, $result->primaryKey);
    }

    public function testSetBulkRegistered() {
        $result = (new Respondent())->setBulkRegistered([
            'c9723dcc-daed-4078-b373-cbe173c03740',
            'df56bf0a-c9b4-4cc2-8458-d17e22a0863d'
        ]);

        $this->assertEquals(2, $result);
    }


    public function testFindByToken(){
        $result= Respondent::findByToken("c9723dcc-daed-4078-b373-cbe173c03740");
        $this->assertInstanceOf(Respondent::class, $result);
    }

    public function testFindByTokenFails(){
        $result= Respondent::findByToken("c9723dcc-daed-4078-b373-cbe173c03740-NOOOOO");
        $this->assertEmpty($result);
    }

}