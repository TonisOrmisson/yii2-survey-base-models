<?php

namespace andmemasin\surveybasemodels\tests\unit;


require_once __DIR__ . '/../TestBaseActive.php';

use andmemasin\surveybasemodels\Rejection;
use andmemasin\surveybasemodels\tests\TestBaseActive;

/**
 * @author Tõnis Ormisson <tonis@andmemasin.eu>
 */
class RejectionTest extends TestBaseActive
{

    /** @var Rejection */
    public $model;

    public $modelClass = Rejection::class;

    /**
     * @return array
     */
    public function baseModelAttributes()
    {
        return array_merge([
            'rejection_id' => 1,
            'survey_id' => 1,
            'respondent_id' => 1,
            'email_address' => "tonis@andmemasin.eu",
            'type' => Rejection::BOUNCE_TYPE_ANSWERED,
            'bounce' => null,
            'time_rejected' => "2010-10-01",

        ], parent::baseModelAttributes());
    }

    public function testTableName() {
        $this->assertEquals('rejection', $this->model::tableName());
    }

    public function testGetBounceTypes() {
        $result = $this->invokeMethod($this->model, 'getBounceTypes');
        $this->assertArrayHasKey('complaint', $result);

    }

}