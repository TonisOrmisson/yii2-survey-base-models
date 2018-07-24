<?php

namespace andmemasin\surveybasemodels\tests\unit;


require_once __DIR__ . '/../TestBaseActive.php';

use andmemasin\surveybasemodels\Respondent;
use andmemasin\surveybasemodels\tests\TestBaseActive;
use Codeception\Stub;
use yii\base\Model;
use yii\helpers\Json;

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
    public function badAddressesProvider()
    {
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
    public function goodAddressesProvider()
    {
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
    public function testValidateEmailFails($address)
    {
        $this->model->email_address = $address;
        $this->assertFalse($this->model->validateEmail('email_address', $this->model->email_address));
    }

    /**
     * @dataProvider goodAddressesProvider
     */
    public function testValidateEmailPasses($address)
    {
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
    public function testValidateEmailFailsDuplicate($address)
    {
        $this->model->email_address = $address;
        /** @var Model $model */
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => true,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());
        $this->assertFalse($this->model->validateEmail('email_address', $this->model->email_address));
    }

    public function testValidateEmailFailsDuplicateMainAddress()
    {
        /** @var Model $model */
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => false,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = "tonis@andmemasin.eu";
        $this->assertFalse($this->model->validateEmail('alternative_email_addresses', $this->model->alternative_email_addresses));
    }

    public function testValidateEmailPassesDuplicateMainAddress()
    {
        /** @var Model $model */
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => false,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = "info@andmemasin.eu";
        $this->assertTrue($this->model->validateEmail('alternative_email_addresses', $this->model->alternative_email_addresses));
    }

    public function testValidateEmailPassesMultipleAddress()
    {
        /** @var Model $model */
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => false,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = Json::encode(["one@andmemasin.eu", "two@andmemasin.eu", "three@andmemasin.eu"]);
        $this->model->validateMultipleEmails('alternative_email_addresses');
        $this->assertTrue(empty($this->model->errors));
    }

    public function testValidateEmailMultipleAddressesSomeDuplicateFails()
    {
        /** @var Model $model */
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => false,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = Json::encode(["1@andmemasin.eu", "2@andmemasin.eu", "1@andmemasin.eu", "2@andmemasin.eu"]);
        $this->model->validateMultipleEmails('alternative_email_addresses');
        $this->assertFalse(empty($this->model->errors));
    }

    public function testValidateEmailMultipleAddressesMaxAmountPasses()
    {
        /** @var Model $model */
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => false,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = Json::encode(["1@andmemasin.eu", "2@andmemasin.eu", "3@andmemasin.eu", "4@andmemasin.eu",
            "5@andmemasin.eu", "6@andmemasin.eu", "7@andmemasin.eu", "8@andmemasin.eu", "9@andmemasin.eu",
            "10@andmemasin.eu", "11@andmemasin.eu", "12@andmemasin.eu", "13@andmemasin.eu", "14@andmemasin.eu",
            "15@andmemasin.eu", "16@andmemasin.eu", "17@andmemasin.eu", "18@andmemasin.eu", "19@andmemasin.eu",
            "20@andmemasin.eu"]);
        $this->model->validateMultipleEmails('alternative_email_addresses');
        $this->assertTrue(empty($this->model->errors));
    }

    public function testValidateEmailMultipleAddressesTooManyFails()
    {
        /** @var Model $model */
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => false,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = Json::encode(["1@andmemasin.eu", "2@andmemasin.eu", "3@andmemasin.eu", "4@andmemasin.eu",
            "5@andmemasin.eu", "6@andmemasin.eu", "7@andmemasin.eu", "8@andmemasin.eu", "9@andmemasin.eu",
            "10@andmemasin.eu", "11@andmemasin.eu", "12@andmemasin.eu", "13@andmemasin.eu", "14@andmemasin.eu",
            "15@andmemasin.eu", "16@andmemasin.eu", "17@andmemasin.eu", "18@andmemasin.eu", "19@andmemasin.eu",
            "20@andmemasin.eu", "21@andmemasin.eu"]);
        $this->model->validateMultipleEmails('alternative_email_addresses');
        $this->assertFalse(empty($this->model->errors));
    }

    public function testGetParticipantDat() {
        $this->assertInternalType('array', $this->model->getParticipantData());
    }
}