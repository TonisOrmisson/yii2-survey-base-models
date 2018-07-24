<?php

namespace andmemasin\surveybasemodels\tests\unit;


require_once __DIR__ . '/../TestBaseActive.php';

use andmemasin\surveybasemodels\Respondent;
use andmemasin\surveybasemodels\tests\TestBaseActive;
use Codeception\Stub;
use yii\base\Model;

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


    /**
     * @return array
     */
    public function badAddressesProvider(){
        return [
            ['my email address'],
            ['my .name@gmail.com'],
            ['my.name @gmail.com'],
            ['my.name@gmail. com'],
            ['my.name@gmail,com'],
            ['my.name@gmail;com'],
            ['my.name@gmail,com'],
            [null],
            [0],
            [1.234],
        ];
    }


    /**
     * @return array
     */
    public function goodAddressesProvider(){
        return [
            ['name@gmail.com'],
            ['my.name@gmail.com'],
            ['mY.nAmE@gmAil.CoM'],
            ['name@amazon.Co.uk'],
        ];
    }


    /**
     * @dataProvider badAddressesProvider
     */
    public function testValidateEmailFails($address) {
        $this->model->email_address = $address;
        $this->assertFalse($this->model->validateEmail('email_address', $this->model->email_address));
    }

    /**
     * @dataProvider goodAddressesProvider
     */
    public function testValidateEmailPasses($address) {
        $this->model->email_address = $address;
        /** @var Model $model */
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => false,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());
        $this->assertTrue($this->model->validateEmail('email_address', $this->model->email_address));
    }

    /**
     * @dataProvider goodAddressesProvider
     */
    public function testValidateEmailFailsDuplicate($address) {
        $this->model->email_address = $address;
        /** @var Model $model */
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => true,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());
        $this->assertFalse($this->model->validateEmail('email_address', $this->model->email_address));
    }



}