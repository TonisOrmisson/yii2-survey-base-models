<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\MyActiveRecord;
use borales\extensions\phoneInput\PhoneInputValidator;
use yii;

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
 *
 * @property boolean $isRejected
 */
class Respondent extends MyActiveRecord
{
    const MAX_ALTERNATIVE_CONTACTS = 20;
    /** @var bool $checkDSNForEmails whether email validation also used DSN records to check if domain exists */
    public static $checkDSNForEmails = true;

    public function init()
    {
        parent::init();
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['survey_id'], 'required'],
            [['survey_id'], 'integer'],
            [['email_address'], 'email'],
            [['email_address'], 'validateEmail'],
            // email addresses always lowercase
            [['email_address','email_address'],'trim'],
            ['email_address','filter', 'filter' => 'strtolower'],
            [['alternative_email_addresses'], 'string'],
            [['alternative_email_addresses'], 'validateMultipleEmails'],
            [['phone_number','email_address'],'trim'],
            ['phone_number','filter', 'filter' => 'strtolower'],
            [['phone_number'],'string'],
            [['phone_number'],'validatePhoneNumber'],
            [['alternative_phone_numbers'], 'string'],
            [['alternative_phone_numbers'],'validateMultiplePhoneNumbers'],
            [['token'], 'unique'],
        ], parent::rules());
    }


    public function validateEmail($attribute,$address = null){
        if(!$address or empty($address)){
            $address = $this->email_address;
            $isSameAsMainAddress = false;
        }else{
            $isSameAsMainAddress = ($attribute=='email_address' ? false : $address == $this->email_address);

        }

        $validator = new yii\validators\EmailValidator();
        $validator->checkDNS = static::$checkDSNForEmails;
        $isValidFormat = $validator->validate($address);

        $isDuplicate = $this->isEmailSurveyDuplicate($address);

        if($isValidFormat && !$isSameAsMainAddress && !$isDuplicate){
            return true;
        }else{
            $reason = '';
            if(!$isValidFormat){
                $reason = Yii::t('app','Invalid email format');
            } else if($isSameAsMainAddress){
                $reason = Yii::t('app',$attribute. ' duplicates main address');
            } else if($isDuplicate) {
                $reason = Yii::t('app','Duplicates some other address');
            }

            $this->addError($attribute,
                Yii::t('app',
                    'Invalid email address "{0}"',[$address]
                ).' '.Yii::t('app','Reason: {0}',[$reason])
            );
        }
        return false;
    }


    public function validateMultipleEmails($attribute){
        if($this->alternative_email_addresses){
            $cleanAddresses = [];
            $addresses = yii\helpers\Json::decode($this->alternative_email_addresses);
            if(!empty($addresses)){
                $i=0;
                foreach ($addresses as $key => $address){
                    // check the alternative numbers of that model for duplicates
                    $checkItems = $addresses;
                    unset($checkItems[$key]);
                    if(in_array($address,$checkItems)){
                        $this->addError($attribute,Yii::t('app','Duplicate email in alternative email addresses'));
                    }

                    $i++;
                    if($i>=static::MAX_ALTERNATIVE_CONTACTS){
                        $this->addError($attribute,Yii::t('app','Maximum alternative addresses limit ({0}) reached for {1}',[static::MAX_ALTERNATIVE_CONTACTS,$this->email_address]));
                    }
                    $address = strtolower(trim($address));
                    if($this->validateEmail($attribute,$address)){
                        $cleanAddresses[]=$address;
                    }
                }
                if(!empty($cleanAddresses)){
                    $this->alternative_email_addresses = yii\helpers\Json::encode($cleanAddresses);
                } else {
                    $this->alternative_email_addresses = null;
                }
            }
        }
    }


    public function validatePhoneNumber($attribute, $phone_number = null){
        if(!$phone_number or empty($phone_number)){
            $phone_number = $this->phone_number;
            $isSameAsMain = false;
        }else{
            $isSameAsMain = ($attribute=='phone_number' ? false : $phone_number == $this->phone_number);
        }
        // TODO
        $isValidFormat = true;

        $isDuplicate = $this->isPhoneSurveyDuplicate($phone_number);

        if($isValidFormat && !$isSameAsMain && !$isDuplicate){
            return true;
        }else{
            $reason = '';
            if(!$isValidFormat){
                $reason = Yii::t('app','Invalid phone number format');
            } else if($isSameAsMain){
                $reason = Yii::t('app',$attribute. ' duplicates main phone number');
            } else if($isDuplicate) {
                $reason = Yii::t('app','Duplicate phone number');
            }

            $this->addError($attribute,
                Yii::t('app',
                    'Invalid phone number "{0}"',[$phone_number]
                ).' '.Yii::t('app','Reason: {0}',[$reason])
            );
        }
        return false;
    }

    public function validateMultiplePhoneNumbers($attribute){
        if($this->alternative_phone_numbers){
            $cleanItems = [];
            $items = yii\helpers\Json::decode($this->alternative_phone_numbers);

            if(!empty($items)){
                $i=0;
                foreach ($items as $key=> $item){
                    $item = strtolower(trim($item));
                    if($item <> ''){

                        // check the alternative numbers of that model for duplicates
                        $checkItems = $items;
                        unset($checkItems[$key]);
                        if(in_array($item,$checkItems)){
                            $this->addError($attribute,Yii::t('app','Duplicate number in alternative phone numbers'));
                        }


                        $i++;
                        if($i>=static::MAX_ALTERNATIVE_CONTACTS){
                            $this->addError($attribute,Yii::t('app','Maximum alternative phone numbers limit ({0}) reached for {1}',[static::MAX_ALTERNATIVE_CONTACTS,$this->phone_number]));
                        }
                        if($this->validatePhoneNumber($attribute,$item)){
                            $cleanItems[]=$item;
                        }

                    }
                }
                if(!empty($cleanItems)){
                    $this->alternative_phone_numbers = yii\helpers\Json::encode($cleanItems);
                } else {
                    $this->alternative_phone_numbers = null;
                }
            }
        }
    }



    /**
     * @param string $phone_number Phone number to check duplicates for
     * @return bool
     */
    public function isPhoneSurveyDuplicate($phone_number){
        $query = static::find();
        // check only this survey
        $query->andWhere(['survey_id'=>$this->survey_id]);

        if($this->respondent_id){
            // not itself
            $query->andWhere(['!=','respondent_id',$this->respondent_id]);
        }

        $condition = ['or',
            '`phone_number`=:phone_number',
            '`alternative_phone_numbers` LIKE :phone_number2',
        ];
        $query->andWhere($condition,[':phone_number'=>$phone_number,':phone_number2'=>'%\"'.$phone_number.'\"%']);
        echo $query->createCommand()->rawSql;
        if($query->count() > 0){
            return true;
        }
        return false;
    }


    /**
     * @param string $email_address Email address to check duplicates for
     * @return bool
     */
    public function isEmailSurveyDuplicate($email_address){
        $query = static::find();
        // check only this survey
        $query->andWhere(['survey_id'=>$this->survey_id]);
        // not itself
        if($this->respondent_id){
            // not itself
            $query->andWhere(['!=','respondent_id',$this->respondent_id]);
        }

        $email_condition = ['or',
            '`email_address`=:email_address',
            '`alternative_email_addresses` LIKE :email_address2',
        ];
        $query->andWhere($email_condition,[':email_address'=>$email_address,':email_address2'=>'%\"'.$email_address.'\"%']);
        if($query->count() > 0){
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
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
    public static function findByToken($token = null){
        if($token){
            /** @var static $model */
            $model = static::find()
                ->andWhere('token=:token',[':token'=>$token])
                ->one();
            return $model;
        }
        return null;
    }

    /**
     * Check whether respondent has rejected this specific survey or has a hard bounce on e_mail address
     * @return bool
     */
    public function getIsRejected(){
        if(Rejection::rejectedByCode($this->token)){
            return true;
        }
        if(Rejection::bouncedByEmailAddress($this->email_address)){
            return true;
        }

        return false;
    }

    /**
     * Get the last respondent based on Email-address
     * @param string $email_address
     * @return static
     */
    public static function getLatestByEmail($email_address){
        /** @var static $model */
        $model = static::find()
            ->andWhere(['email_address'=>$email_address])
            ->orderBy([static::primaryKey()[0]=>SORT_DESC])
            ->one();
        return $model;
    }

}