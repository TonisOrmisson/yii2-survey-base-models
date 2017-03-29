<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\MyActiveRecord;
use andmemasin\surveybasemodels\Survey;
use yii;

/**
 * This is the model class for a generic Respondent.
 *
 * @property int $respondent_id
 * @property int $survey_id
 * @property string $token
 * @property Survey $survey
 * @property string $email_address
 *
 */
class Respondent extends MyActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['survey_id'], 'required'],
            [['survey_id'], 'integer'],
            [['email_address'], 'email'],
            [['token'], 'unique'],
        ], parent::rules());
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

}