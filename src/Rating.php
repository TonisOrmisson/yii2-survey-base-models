<?php

namespace andmemasin\surveybasemodels;

use Yii;
use andmemasin\myabstract\MyActiveRecord;

/**
 * This is the model class for table "rating".
 *
 * @property integer $rating_id
 * @property integer $respondent_id
 * @property integer $survey_id
 * @property integer $sample_id
 * @property integer $value
 * @property string $comment
 *
 * @property Respondent $respondent
 */
class Rating extends MyActiveRecord
{
    public $initRespondent;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rating';
    }
    
    /**
     * {@inheritdoc}
     */
    public static function modelName()
    {
        return Yii::t('app','Rating');
    }

     

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge([
            [['respondent_id', 'survey_id', 'sample_id', 'value'], 'required'],
            [['respondent_id', 'survey_id', 'sample_id', 'value'], 'integer'],
            [['comment'], 'string'],
            [['respondent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Respondent::class, 'targetAttribute' => ['respondent_id' => 'respondent_id']],
        ],  parent::rules());
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rating_id' => Yii::t('app', 'Rating ID'),
            'respondent_id' => Yii::t('app', 'Respondent'),
            'survey_id' => Yii::t('app', 'Survey'),
            'sample_id' => Yii::t('app', 'Sample'),
            'value' => Yii::t('app', 'Rating value'),
            'comment' => Yii::t('app', 'Comment'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRespondent()
    {
        return $this->hasOne(Respondent::class, ['respondent_id' => 'respondent_id']);
    }

}
