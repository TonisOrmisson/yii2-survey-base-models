<?php

namespace andmemasin\surveybasemodels\tests;

require_once __DIR__ . '/TestBase.php';

use andmemasin\myabstract\MyActiveRecord;
use yii;


class TestBaseActive extends TestBase
{
    /** @var MyActiveRecord */
    public $model;
    /**
     * @return array
     */
    public function baseModelAttributes()
    {
        return [
            'user_created' => 1,
            'user_updated' => 1,
            'user_closed' => null,
            'time_created' => "2010-10-01",
            'time_updated' => "2010-10-01",
            'time_closed' => null,

        ];
    }



    public function testModelName() {
        $this->assertInternalType('string', $this->model::modelName());
    }

}