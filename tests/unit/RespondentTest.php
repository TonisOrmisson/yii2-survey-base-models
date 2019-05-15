<?php

namespace andmemasin\surveybasemodels\tests\unit;


require_once __DIR__ . '/../TestBaseActive.php';

use andmemasin\surveybasemodels\Respondent;
use andmemasin\surveybasemodels\tests\TestBaseActive;
use Codeception\Stub;
use Ramsey\Uuid\Uuid;
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
            'key' => 'yks',
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
        $this->goodModel();
        $this->model->email_address = $address;
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
        $this->goodModel();
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = "tonis@andmemasin.eu";
        $this->assertFalse($this->model->validateEmail('alternative_email_addresses', $this->model->alternative_email_addresses));
    }

    public function testValidateEmailPassesDuplicateMainAddress()
    {
        $this->goodModel();
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = "info@andmemasin.eu";
        $this->assertTrue($this->model->validateEmail('alternative_email_addresses', $this->model->alternative_email_addresses));
    }

    public function testValidateEmailPassesMultipleAddress()
    {
        $this->goodModel();
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = Json::encode(["one@andmemasin.eu", "two@andmemasin.eu", "three@andmemasin.eu"]);
        $this->model->validateMultipleEmails('alternative_email_addresses');
        $this->assertTrue(empty($this->model->errors));
    }

    public function testValidateEmailMultipleAddressesSomeDuplicateFails()
    {
        $this->goodModel();
        $this->model->email_address = "tonis@andmemasin.eu";
        $this->model->alternative_email_addresses = Json::encode(["1@andmemasin.eu", "2@andmemasin.eu", "1@andmemasin.eu", "2@andmemasin.eu"]);
        $this->model->validateMultipleEmails('alternative_email_addresses');
        $this->assertFalse(empty($this->model->errors));
    }

    public function testValidateEmailMultipleAddressesMaxAmountPasses()
    {
        $this->goodModel();
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
        $this->goodModel();
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


    public function testValidatePhoneNumbers()
    {
        $this->goodModel();
        $this->model->phone_number = "12345677";
        $this->model->alternative_phone_numbers = null;
        $this->model->validateMultiplePhoneNumbers('alternative_phone_numbers');
        $this->assertTrue(empty($this->model->errors));
    }

    public function testValidatePhoneNumbersMaxAmountPasses()
    {
        $this->goodModel();
        $this->model->phone_number = "12345677";
        $this->model->alternative_phone_numbers = Json::encode(["123456 1", "123456 2", "123456 3", "123456 4",
            "123456 5", "123456 6", "123456 7", "123456 8", "123456 9", "123456 10", "123456 11", "123456 12",
            "123456 13", "123456 14", "123456 15", "123456 16", "123456 17", "123456 18", "123456 19",
            "123456 20"]);
        $this->model->validateMultiplePhoneNumbers('alternative_phone_numbers');
        $this->assertTrue(empty($this->model->errors));
    }

    public function testValidatePhoneNumbersOverMaxAmountFails()
    {
        $this->goodModel();
        $this->model->phone_number = "12345677";
        $this->model->alternative_phone_numbers = Json::encode(["123456 1", "123456 2", "123456 3", "123456 4",
            "123456 5", "123456 6", "123456 7", "123456 8", "123456 9", "123456 10", "123456 11", "123456 12",
            "123456 13", "123456 14", "123456 15", "123456 16", "123456 17", "123456 18", "123456 19",
            "123456 20", "123456 21"]);
        $this->model->validateMultiplePhoneNumbers('alternative_phone_numbers');
        $this->assertFalse(empty($this->model->errors));
    }

    public function testValidatePhoneNumbersInternalDuplicatesFails()
    {
        $this->goodModel();
        $this->model->phone_number = "12345677";
        $this->model->alternative_phone_numbers = Json::encode(["123456 1", "123456 2", "123456 1", "123456 2"]);
        $this->model->validateMultiplePhoneNumbers('alternative_phone_numbers');
        $this->assertFalse(empty($this->model->errors));
    }

    public function testValidatePhoneNumbersSameAsMainNumberFails()
    {
        $this->goodModel();
        $this->model->phone_number = "12345677";
        $this->model->alternative_phone_numbers = Json::encode(["12345677", "123456 2", "123456 1", "123456 2"]);
        $this->model->validateMultiplePhoneNumbers('alternative_phone_numbers');
        $this->assertFalse(empty($this->model->errors));
    }

    public function testValidatePhoneNumbersNoAlternativesPasses()
    {
        $this->goodModel();
        $this->model->phone_number = "12345677";
        $this->model->alternative_phone_numbers = null;
        $this->model->validateMultiplePhoneNumbers('alternative_phone_numbers');
        $this->assertTrue(empty($this->model->errors));
    }

    public function testValidatePhoneNumberTooShortFails()
    {
        $this->goodModel();
        $this->model->phone_number = "12";
        $this->model->validate('phone_number');
        $this->assertFalse(empty($this->model->errors));
    }

    public function testValidatePhoneNumberMinShortPasses()
    {
        $this->goodModel();
        $this->model->phone_number = "1234";
        $this->model->validate('phone_number');
        $this->assertTrue(empty($this->model->errors));
    }

    public function testValidatePhoneNumberMaxLongPasses()
    {
        $this->goodModel();
        $this->model->phone_number = "12345678901234567890123456789012";
        $this->model->validate('phone_number');
        $this->assertTrue(empty($this->model->errors));
    }

    public function testValidatePhoneNumberTooLongFails()
    {
        $this->goodModel();
        $this->model->phone_number = "123456789012345678901234567890123";
        $this->model->validate('phone_number');
        $this->assertFalse(empty($this->model->errors));
    }

    public function testGetShortToken() {
        $this->goodModel();
        $this->model->token = "4e52c919-513e-4562-9248-7dd612c6c1ca";
        $this->assertEquals("fpfyRTmt6XeE9ehEKZ5LwF", $this->model->shortToken);
    }

    public function testGetShortTokenNoUUID() {
        $this->goodModel();
        $this->assertEquals($this->model->token, $this->model->shortToken);
    }

    private function goodModel() {
        $this->model = Stub::make($this->modelClass, [
            'attributes' => array_keys($this->baseModelAttributes()),
            'isEmailSurveyDuplicate' => false,
            'validatePhoneSurveyDuplicate' => null,
        ]);
        $this->model->setAttributes($this->baseModelAttributes());

    }


}