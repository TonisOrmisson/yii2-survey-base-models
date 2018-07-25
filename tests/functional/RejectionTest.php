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

    public function testRejectedByCode() {
        $result= Rejection::rejectedByCode("c9723dcc-daed-4078-b373-cbe173c03740");
        $this->assertTrue($result);
    }

    public function testRejectedByCodeFails() {
        $result= Rejection::rejectedByCode("no-such-thing");
        $this->assertFalse($result);
    }

    public function testFindHardBounces() {
        $result= Rejection::findHardBounces("me@example.com");
        $this->assertEquals(0, count($result));
        $result= Rejection::findHardBounces("me2@example.com");
        $this->assertEquals(1, count($result));
    }

    public function testHasBouncedHard() {
        $result= Rejection::hasBouncedHard("me2@example.com");
        $this->assertTrue($result);
    }

    public function testHasBouncedHardFails() {
        $result= Rejection::hasBouncedHard("me@example.com");
        $this->assertFalse($result);
    }
}