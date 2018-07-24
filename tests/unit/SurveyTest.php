<?php

namespace andmemasin\surveybasemodels\tests\unit;


require_once __DIR__ . '/../TestBaseActive.php';

use andmemasin\surveybasemodels\Survey;
use andmemasin\surveybasemodels\tests\TestBaseActive;
use yii\helpers\Json;

/**
 * @author Tõnis Ormisson <tonis@andmemasin.eu>
 */
class SurveyTest extends TestBaseActive
{

    /** @var Survey */
    public $model;

    public $modelClass = Survey::class;

    /**
     * @return array
     */
    public function baseModelAttributes()
    {
        return array_merge([
            'survey_id' => 1,
            'name' => "test-survey",
            'status' => "active",
            'options' => Json::encode([1,2,3]),
            'key' => "124dfdf9-9c47-4bf5-8df2-944bfe84111d",

        ], parent::baseModelAttributes());
    }

    public function testGetOptionsDecoded() {
        $this->assertEquals([1,2,3], $this->model->getOptionsDecoded());
    }


}