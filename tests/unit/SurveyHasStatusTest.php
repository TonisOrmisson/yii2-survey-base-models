<?php

namespace andmemasin\surveybasemodels\tests\unit;


require_once __DIR__ . '/../TestBaseActive.php';

use andmemasin\surveybasemodels\Survey;
use andmemasin\surveybasemodels\SurveyHasStatus;
use andmemasin\surveybasemodels\tests\TestBaseActive;
use yii\helpers\Json;

/**
 * @author Tõnis Ormisson <tonis@andmemasin.eu>
 */
class SurveyHasStatusTest extends TestBaseActive
{

    /** @var SurveyHasStatus */
    public $model;

    public $modelClass = SurveyHasStatus::class;

    /**
     * @return array
     */
    public function baseModelAttributes()
    {
        return array_merge([
            'survey_has_status_id' => 1,
            'survey_id' => 1,
            'status' => "active",

        ], parent::baseModelAttributes());
    }

    public function testTableName() {
        $this->assertEquals('survey_has_status', $this->model::tableName());
    }


}