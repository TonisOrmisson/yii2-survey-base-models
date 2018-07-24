<?php

namespace andmemasin\surveybasemodels\tests;

use Codeception\Stub;
use Codeception\Test\Unit;
use yii;
use andmemasin\myabstract\test\ModelTestTrait;


abstract class TestBase extends Unit
{
    use ModelTestTrait;

    /** @var yii\base\Model */
    public $model;

    /** @var string */
    public $modelClass;

    /**
     * @var \UnitTester
     */
    protected $tester;


    protected function _before()
    {
        $this->model = $this->baseObject();
    }


    /**
     * @return array
     */
    abstract public function baseModelAttributes();


    /**
     * @return yii\base\Model
     */
    public function baseObject(){
        /** @var yii\base\Model $model */
        $model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
        ]);
        $model->setAttributes($this->baseModelAttributes());
        return $model;
    }

}