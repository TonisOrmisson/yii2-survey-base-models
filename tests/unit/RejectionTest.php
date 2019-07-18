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
            'bounce' => '{"token": "a-bounce-token", "deliverystatus": "5.1.1", "addresser": "example@example.com", "rhost": "", "action": "failed", "diagnostictype": "SMTP", "messageid": "01020166d370b46b-a936dc65-3223-4a51-bd23-e3b07c227379-000000@example.com", "alias": "", "catch": "", "smtpagent": "Email::AmazonSES", "lhost": "example.com", "senderdomain": "example.com", "listid": "", "smtpcommand": "", "feedbacktype": "", "diagnosticcode": "550 5.1.1 <someone@example.com>: Recipient address rejected: User unknown in virtual mailbox table", "reason": "userunknown", "timestamp": 1541152870, "subject": "Email subject", "softbounce": 0, "replycode": "550", "timezoneoffset": "+0000", "destination": "example.com", "recipient": "someone@example.com"}',
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

    public function testgetBounceObjectIsEmpty()
    {
        $this->model->bounce = null;
        $this->assertNull($this->model->bounceObject);
        $this->assertNull($this->model->bounceReplyCode);
        $this->assertNull($this->model->bounceReason);
    }

    public function testgetBounceObjectLoadedReturnsObject()
    {
        $this->assertInstanceOf('stdClass', $this->model->bounceObject);
    }

    public function testGetBounceReason()
    {
        $this->assertEquals('550 5.1.1 <someone@example.com>: Recipient address rejected: User unknown in virtual mailbox table', $this->model->bounceReason);
    }

    public function testGetBounceReplyCode()
    {
        $this->assertEquals('5.1.1', $this->model->bounceReplyCode);
    }

}