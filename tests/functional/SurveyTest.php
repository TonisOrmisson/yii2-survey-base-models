<?php

namespace andmemasin\surveybasemodels\tests\functional;


use andmemasin\surveybasemodels\Rating;
use andmemasin\surveybasemodels\Survey;
use Codeception\Test\Unit;
use Yii;


class SurveyTest extends Unit
{
    public function testFindByKey() {
        $model = Survey::findByKey("7507f1dc-8854-4b33-8860-87600b46aad3");
        $this->assertInstanceOf(Survey::class, $model);
    }

}