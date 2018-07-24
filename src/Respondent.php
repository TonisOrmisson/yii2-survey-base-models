<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\MyActiveRecord;
use PascalDeVink\ShortUuid\ShortUuid;
use Ramsey\Uuid\Uuid;
use yii;
use andmemasin\helpers\DateHelper;

/**
 * This is the model class for a generic Respondent.
 *
 * @property int $respondent_id
 * @property int $survey_id
 * @property string $token
 * @property Survey $survey
 * @property string $email_address
 * @property string $alternative_email_addresses Inserted as CSV, stored as JSON
 * @property string $phone_number
 * @property string $alternative_phone_numbers Inserted as CSV, stored as JSON
 * @property string $time_collector_registered
 *
 * @property boolean $isRejected
 * @property string $shortToken If the token is uuid, then short-uuid will be returned
 */
class Respondent extends MyActiveRecord
{
    const MAX_ALTERNATIVE_CONTACTS = 20;
    /** @var bool $checkDSNForEmails whether email validation also used DSN records to check if domain exists */
    public static $checkDSNForEmails = true;

    /**
     * @var array $surveyIdentifyingColumns names of the columns that identify a respondent as unique inside a survey
     */
    public static $surveyIdentifyingColumns = ['phone_number', 'email_address'];

    public function init()
    {
        parent::init();
    }


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge([
            [['survey_id', 'token'], 'required'],
            [['survey_id'], 'integer'],
            [['email_address'], 'validateEmail'],
            [['email_address', 'phone_number'], 'filter', 'filter' => 'trim'],
            [['email_address', 'phone_number'], 'filter', 'filter' => 'strtolower'],
            [['alternative_email_addresses'], 'string'],
            [['alternative_email_addresses'], 'validateMultipleEmails'],
            [['phone_number'], 'string'],
            [['phone_number'], 'validatePhoneNumber'],
            [['time_collector_registered'], 'string', 'max' => 32],
            [['alternative_phone_numbers'], 'string'],
            [['alternative_phone_numbers'], 'validateMultiplePhoneNumbers'],
            [['token'], 'unique'],
        ], parent::rules());
    }


    /**
     * @param string $attribute
     * @param string $address
     * @return bool
     */
    public function validateEmail($attribute, $address)
    {
        if (empty($address)) {
            $address = $this->email_address;
        }
        if (!empty($address)) {
            if ($this->validateEmailFormat($attribute, $address)
                && !$this->isSameAsMainAddress($attribute, $address)
                && !$this->isEmailSurveyDuplicate($attribute, $address)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $attribute
     * @param string $address
     * @return bool
     */
    private function validateEmailFormat($attribute, $address)
    {
        $validator = new yii\validators\EmailValidator();
        $validator->checkDNS = static::$checkDSNForEmails;
        if (!$validator->validate($address)) {
            $this->addError($attribute,
                Yii::t('app',
                    'Invalid email address "{0}"', [$address]
                ) . ' ' . Yii::t('app', 'Reason: {0}', [Yii::t('app', 'Invalid email format')])
            );
            return false;
        }
        return true;
    }

    /**
     * @param string $address
     * @return bool
     */
    private function isSameAsMainAddress($attribute, $address)
    {
        if (empty($address) | $attribute === "email_address") {
            return false;
        }
        if ($address === $this->email_address) {
            $this->addError($attribute,
                Yii::t('app',
                    'Email address same as main addrress "{0}"', [$address]
                ) . ' ' . Yii::t('app', 'Reason: {0}', [Yii::t('app', $attribute . ' duplicates main address')])
            );
            return true;
        }
        return false;
    }


    public function validateMultipleEmails($attribute)
    {
        $cleanAddresses = [];
        $addresses = yii\helpers\Json::decode($this->alternative_email_addresses);
        if (!empty($addresses)) {
            $i = 0;
            foreach ($addresses as $key => $address) {
                $i++;
                // check the alternative numbers of that model for duplicates
                $checkItems = $addresses;
                unset($checkItems[$key]);
                if (in_array($address, $checkItems)) {
                    $this->addError($attribute, Yii::t('app', 'Duplicate email in alternative email addresses'));
                }

                if ($i > static::MAX_ALTERNATIVE_CONTACTS) {
                    $this->addError($attribute, Yii::t('app', 'Maximum alternative addresses limit ({0}) reached for {1}', [static::MAX_ALTERNATIVE_CONTACTS, $this->email_address]));
                }
                $address = strtolower(trim($address));
                if ($this->validateEmail($attribute, $address)) {
                    $cleanAddresses[] = $address;
                }
            }
        }
        $this->alternative_email_addresses = yii\helpers\Json::encode($cleanAddresses);
    }


    public function validatePhoneNumber($attribute, $phone_number)
    {
        if (empty($phone_number)) {
            $phone_number = $this->phone_number;
        }
        if (!empty($phone_number)) {
            $this->validateSameAsMainNumber($attribute, $phone_number);
            // TODO
            $isValidFormat = true;
            $this->validatePhoneSurveyDuplicate($attribute, $phone_number);

        }

    }


    public function validateMultiplePhoneNumbers($attribute)
    {
        $cleanItems = [];
        $items = yii\helpers\Json::decode($this->alternative_phone_numbers);
        if (!empty($items)) {
            $i = 0;
            foreach ($items as $key => $item) {
                $i++;
                $item = strtolower(trim($item));
                $this->validateAlternativePhoneNumberInternalDuplicates($attribute, $item, $key);

                if ($i >= static::MAX_ALTERNATIVE_CONTACTS) {
                    $this->addError($attribute, Yii::t('app', 'Maximum alternative phone numbers limit ({0}) reached for {1}', [static::MAX_ALTERNATIVE_CONTACTS, $this->phone_number]));
                }
               $this->validatePhoneNumber($attribute, $item);
            }
        }
        $this->alternative_phone_numbers = yii\helpers\Json::encode($cleanItems);
    }

    private function validateAlternativePhoneNumberInternalDuplicates($attribute, $number, $key)
    {
        $items = yii\helpers\Json::decode($this->alternative_phone_numbers);
        $checkItems = $items;
        unset($checkItems[$key]);
        if (in_array($number, $checkItems)) {
            $this->addError($attribute, Yii::t('app', 'Duplicate number in alternative phone numbers'));
        }
    }


    /**
     * @param string $attribute
     * @param string $number
     * @return null
     */
    private function validateSameAsMainNumber($attribute, $number)
    {
        if (empty($number) | $attribute === 'phone_number') {
            return null;
        }

        if ($number === $this->phone_number) {
            $this->addError($attribute,
                Yii::t('app',
                    'Invalid phone number "{0}"', [$number]
                ) . ' ' . Yii::t('app', 'Reason: {0}', [Yii::t('app', $attribute . ' duplicates main phone number')])
            );
        }
        return null;
    }


    /**
     * @param string $attribute
     * @param string $phone_number Phone number to check duplicates for
     */
    private function validatePhoneSurveyDuplicate($attribute, $phone_number)
    {
        $query = static::find();
        // check only this survey
        $query->andWhere(['survey_id' => $this->survey_id]);

        if ($this->respondent_id) {
            // not itself
            $query->andWhere(['!=', 'respondent_id', $this->respondent_id]);
        }

        $condition = ['or',
            '`phone_number`=:phone_number',
            '`alternative_phone_numbers` LIKE :phone_number2',
        ];

        $query->andWhere($condition, [':phone_number' => $phone_number, ':phone_number2' => '%\"' . $phone_number . '\"%']);

        if ($query->count() > 0) {
            $this->addError($attribute,
                Yii::t('app',
                    'Invalid phone number "{0}"', [$phone_number]
                ) . ' ' . Yii::t('app', 'Reason: {0}', [Yii::t('app', 'Duplicate phone number')])
            );
        }
    }


    /**
     * @param string $email_address Email address to check duplicates for
     * @return bool
     */
    public function isEmailSurveyDuplicate($attribute, $email_address)
    {
        $query = static::find();
        // check only this survey
        $query->andWhere(['survey_id' => $this->survey_id]);

        // not itself
        if (!empty($this->respondent_id)) {
            // not itself
            $query->andWhere(['!=', 'respondent_id', $this->respondent_id]);
        }

        $email_condition = ['or',
            '`email_address`=:email_address',
            '`alternative_email_addresses` LIKE :email_address2',
        ];

        $query->andWhere($email_condition, [':email_address' => $email_address, ':email_address2' => '%\"' . $email_address . '\"%']);

        if ($query->count() > 0) {
            $this->addError($attribute,
                Yii::t('app',
                    'Invalid email address "{0}"', [$email_address]
                ) . ' ' . Yii::t('app', 'Reason: {0}', [Yii::t('app', 'Duplicates some other address')])
            );
            return true;
        }


        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'token' => Yii::t('app', 'Unique Token'),
        ];
    }

    /**
     * @param string $token Respondents token
     * @return static
     */
    public static function findByToken($token = null)
    {
        if ($token) {
            $models = self::findByTokens([$token]);
            if (!empty($models)) {
                return $models[0];
            }
        }
        return null;
    }

    /**
     * @param string[] $tokens Respondents tokens
     * @return static[]
     */
    public static function findByTokens($tokens)
    {
        /** @var static[] $model */
        $models = static::find()
            ->andWhere(['in', 'token', $tokens])
            ->all();
        return $models;
    }

    /**
     * Check whether respondent has rejected this specific survey or has a hard bounce on e_mail address
     * @return bool
     */
    public function getIsRejected()
    {
        if (Rejection::rejectedByCode($this->token)) {
            return true;
        }

        if (Rejection::bouncedByEmailAddress($this->email_address)) {
            return true;
        }

        return false;
    }

    /**
     * Get the last respondent based on Email-address
     * @param string $email_address
     * @return static
     */
    public static function getLatestByEmail($email_address)
    {
        /** @var static $model */
        $model = static::find()
            ->andWhere(['email_address' => $email_address])
            ->orderBy([static::primaryKey()[0] => SORT_DESC])
            ->one();
        return $model;
    }

    /**
     * @return string
     */
    public function getShortToken()
    {
        if (Uuid::isValid($this->token)) {
            $uuid = Uuid::fromString($this->token);
            $shotUuid = new ShortUuid();
            return $shotUuid->encode($uuid);
        }
        return $this->token;
    }

    /**
     * TODO move to some factory
     */
    public function setBulkRegistered($tokens, $field = 'time_collector_registered')
    {
        $query = new yii\db\Query();
        $dateHelper = new DateHelper();
        $query->createCommand()
            ->update(self::tableName(),
                [$field=> $dateHelper->getDatetime6()],
                ['in','token',$tokens]
            )->execute();
    }

    /**
     * @return array
     */
    public function getParticipantData(){
        return [];
    }


}