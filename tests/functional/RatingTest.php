<?php

namespace andmemasin\collector\tests\functional;


use andmemasin\surveybasemodels\Rating;
use andmemasin\surveybasemodels\Respondent;
use Codeception\Test\Unit;
use Yii;


class RatingTest extends Unit
{
    public function testGetRespondent() {
        $model = Rating::findOne(1);
        $this->assertInstanceOf(Respondent::class, $model->respondent);
    }

}