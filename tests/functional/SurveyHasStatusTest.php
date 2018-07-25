<?php

namespace andmemasin\surveybasemodels\tests\functional;


use andmemasin\surveybasemodels\Survey;
use andmemasin\surveybasemodels\SurveyHasStatus;
use Codeception\Test\Unit;
use andmemasin\survey\Status;

class SurveyHasStatusTest extends Unit
{
    public function testCreate() {
        $model = Survey::findOne(1);
        $result = SurveyHasStatus::create($model, Status::STATUS_INACTIVE);
        $this->assertTrue($result);
    }

}