<?php

namespace andmemasin\surveybasemodels\tests\unit;


require_once __DIR__ . '/../TestBaseActive.php';

use andmemasin\surveybasemodels\Rating;
use andmemasin\surveybasemodels\tests\TestBaseActive;

/**
 * @author Tõnis Ormisson <tonis@andmemasin.eu>
 */
class RatingTest extends TestBaseActive
{

    /** @var Rating */
    public $model;

    public $modelClass = Rating::class;

    /**
     * @return array
     */
    public function baseModelAttributes()
    {
        return array_merge([
            'rating_id' => 1,
            'respondent_id' => 1,
            'survey_id' => 1,
            'sample_id' => 1,
            'value' => 3.0,
            'comment' => "comment",

        ], parent::baseModelAttributes());
    }
    public function testTableName() {
        $this->assertEquals('rating', $this->model::tableName());
    }

}