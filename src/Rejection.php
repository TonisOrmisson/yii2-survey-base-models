<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\MyActiveRecord;
use yii;

/**
 *
 * @property integer $rejection_id
 * @property integer $survey_id
 * @property integer $respondent_id
 * @property string $email_address
 * @property string $type
 * @property string $bounce
 * @property string $time_rejected
 *
 * @property Respondent $respondent
 * @property Survey $survey
 * @property \stdClass $bounceObject The bounce as object
 * @property string $bounceReason
 * @property string $bounceReplyCode
 */
class Rejection extends MyActiveRecord
{
    const BOUNCE_TYPE_HARD = 'hard';
    const BOUNCE_TYPE_SOFT = 'soft';
    const BOUNCE_TYPE_COMPLAINT = 'complaint';
    const BOUNCE_TYPE_ANSWERED = 'answered';
    const BOUNCE_TYPE_OTHER = 'other';

    /** @var string  */
    protected $respondentClass = Respondent::class;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rejection';
    }
    

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge([
            [['survey_id', 'respondent_id'], 'integer'],
            [['time_rejected'], 'required'],
            [['bounce'], 'string'],
            [['time_rejected'], 'safe'],
            [['email_address'], 'string', 'max' => 255],
            [['email_address'], 'email'],
            [['type'], 'string', 'max' => 45],
            [['respondent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Respondent::class, 'targetAttribute' => ['respondent_id' => 'respondent_id']],
            [['survey_id'], 'exist', 'skipOnError' => true, 'targetClass' => Survey::class, 'targetAttribute' => ['survey_id' => 'survey_id']],
        ],  parent::rules());
    }


    /**
     * @param string $email_address
     * @return bool
     */
    public static function hasBouncedHard($email_address){
        return (!empty(self::findHardBounces($email_address)));
    }



    /**
     * @param string $email_address
     * @return Rejection[]
     */
    public static function findHardBounces($email_address){
        return Rejection::find()
            ->andWhere(['email_address'=>$email_address, 'type' => self::BOUNCE_TYPE_HARD])
            ->all();
    }

    /**
     * Check whether we have a bounce registered from this email
     * @param string $email_address
     * @param string $type
     * @return bool
     */
    public static function bouncedByEmailAddress($email_address,$type = self::BOUNCE_TYPE_HARD){
        $rejections = self::find()
            ->andWhere('email_address=:email_address', [':email_address' => $email_address])
            ->andWhere('type=:type', [':type' => $type]);
        return $rejections->count() > 0;
    }

    /**
     * @param string $email_address
     * @return Rejection
     */
    public static function findByEmail($email_address){
        return Rejection::findOne(['email_address'=>$email_address]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSurvey()
    {
        return $this->hasOne(Survey::class, ['survey_id' => 'survey_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRespondent()
    {
        return $this->hasOne($this->respondentClass, ['respondent_id' => 'respondent_id']);
    }

    public static function getBounceTypes(){
        return [
            self::BOUNCE_TYPE_COMPLAINT => Yii::t('app','Complaint'),
            self::BOUNCE_TYPE_SOFT => Yii::t('app','Soft bounce'),
            self::BOUNCE_TYPE_HARD => Yii::t('app','Hard bounce'),
            self::BOUNCE_TYPE_ANSWERED => Yii::t('app','Respondent has answered already'),
            self::BOUNCE_TYPE_OTHER => Yii::t('app','Other'),
        ];
    }

    public function getBounceObject()
    {
        $object = json_decode($this->bounce);
        if (empty($object)) {
            return null;
        }
        return $object;
    }

    /**
     * @return null|string
     */
    public function getBounceReason()
    {
        if (empty($this->bounceObject)) {
            return null;
        }

        if(isset($this->bounceObject->diagnosticcode)) {
            return $this->bounceObject->diagnosticcode;
        }
        if(isset($this->bounceObject->reason)) {
            return $this->bounceObject->reason;
        }
        return null;
    }

    /**
     * @return null|string
     */
    public function getBounceReplyCode()
    {
        if (empty($this->bounceObject)) {
            return null;
        }

        if(isset($this->bounceObject->deliverystatus)) {
            return $this->bounceObject->deliverystatus;
        }

        return null;
    }
}
