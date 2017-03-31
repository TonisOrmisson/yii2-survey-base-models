<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\MyActiveRecord;
use yii;
use yii\helpers\StringHelper;

/**
 * This is the model class for a generic Respondent.
 *
 * @property int $respondent_id
 * @property int $survey_id
 * @property string $token
 * @property Survey $survey
 * @property string $email_address
 * @property string[] $alternative_email_addresses Inserted as CSV, stored as JSON, returned as string[]
 *
 * @property boolean $isRejected
 */
class Respondent extends MyActiveRecord
{
    const MAX_ALTERNATIVE_EMAILS = 20;

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
            ['email_address','filter', 'filter' => 'strtolower'],
            [['alternative_email_addresses'], 'string'],
            [['alternative_email_addresses'], 'validateMultipleEmails'],
            [['token'], 'unique'],
        ], parent::rules());
    }


    public function validateEmail($attribute,$address = null){
        if(!$address or empty($address)){
            $address = $this->email_address;
        }

        $validator = new yii\validators\EmailValidator();
        $isValidFormat = $validator->validate($address);


        $isSameAsMainAddress = ($attribute=='email_address' ? false : $address == $this->email_address);
        $isDuplicate = $this->isEmailSurveyDuplicate($address);

        if($isValidFormat && !$isSameAsMainAddress && !$isDuplicate){
            return true;
        }else{
            $reason = '';
            if(!$isValidFormat){
                $reason = Yii::t('app','Invalid email format');
            } else if($isSameAsMainAddress){
                $reason = Yii::t('app','Duplicates main address');
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
        if($this->alternative_email_addresses && trim($this->alternative_email_addresses)<>''){
            $addresses = StringHelper::explode($this->alternative_email_addresses);
            $cleanAddresses = [];
            if(!empty($addresses)){
                $i=0;
                foreach ($addresses as $address){
                    $i++;
                    if($i>=static::MAX_ALTERNATIVE_EMAILS){
                        $this->addError($attribute,Yii::t('app','Maximum alternative addresses limit ({0}) reached for {1}',[static::MAX_ALTERNATIVE_EMAILS,$this->email_address]));
                    }
                    $address = strtolower(trim($address));
                    if($this->validateEmail($attribute,$address)){
                        $cleanAddresses[]=$address;
                    }
                }
                $this->alternative_email_addresses = yii\helpers\Json::encode($cleanAddresses);
            }
        }
    }

    /** @inheritdoc */
    public function afterFind()
    {
        parent::afterFind();
        $this->alternative_email_addresses = yii\helpers\Json::decode($this->alternative_email_addresses,true);
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
        $query->andWhere(['!=','respondent_id',$this->respondent_id]);

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
     * @return static|boolean
     */
    public static function findByToken($token = null){
        if($token){
            /** @var static $model */
            $model = static::find()
                ->andWhere('token=:token',[':token'=>$token])
                ->one();
            return $model;
        }
        return false;
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