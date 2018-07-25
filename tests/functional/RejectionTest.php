<?php

namespace andmemasin\surveybasemodels\tests\functional;


use andmemasin\surveybasemodels\Rejection;
use andmemasin\surveybasemodels\Respondent;
use andmemasin\surveybasemodels\Survey;
use Codeception\Test\Unit;
use Yii;


class RejectionTest extends Unit
{
    public function testGetRespondent() {
        $model = Rejection::findOne(1);
        $this->assertInstanceOf(Respondent::class, $model->respondent);
    }

    public function testGetSurvey() {
        $model = Rejection::findOne(1);
        $this->assertInstanceOf(Survey::class, $model->survey);
    }
    public function testFindByEmail() {
        $model = Rejection::findByEmail("me@example.com");
        $this->assertInstanceOf(Rejection::class, $model);
    }
    public function testBouncedByEmailAddress() {
        $result= Rejection::bouncedByEmailAddress("me@example.com", Rejection::BOUNCE_TYPE_COMPLAINT);
        $this->assertTrue($result);
    }
    public function testBouncedByEmailAddressFails() {
        $result= Rejection::bouncedByEmailAddress("no-such-thing");
        $this->assertFalse($result);
    }
}