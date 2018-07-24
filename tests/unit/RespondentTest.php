<?php

namespace andmemasin\surveybasemodels\tests\unit;


require_once __DIR__ . '/../TestBaseActive.php';

use andmemasin\surveybasemodels\Respondent;
use andmemasin\surveybasemodels\tests\TestBaseActive;

/**
 * @author Tõnis Ormisson <tonis@andmemasin.eu>
 */
class RespondentTest extends TestBaseActive
{

    /** @var Respondent */
    public $model;

    public $modelClass = Respondent::class;

    /**
     * @return array
     */
    public function baseModelAttributes()
    {
        return array_merge([
            'respondent_id' => 1,
            'survey_id' => 1,
            'token' => 'test-token',
            'email_address' => "tonis@andmemasin.eu",
            'alternative_email_addresses' => null,
            'phone_number' => "+372 51 234 5",
            'alternative_phone_numbers' => null,
            'time_collector_registered' => null,

        ], parent::baseModelAttributes());
    }



}