<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\ModelWithHasStatus;
use yii;

/**
 * This is the model class for a generic Survey. This describes common
 * parameters all Survey models must have regardless of the methodology.
 * All Surveys in multiple modes (CATI, CAPI, PANEL, WEB etc.) must extend
 * this class.
 *
 * @property int $survey_id
 * @property string $key
 * @property string $status
 * @property string $name
 * @property string $options The options as json string. Contains the Collector authentication information (or any other info)
 *
 *
 */
class Survey extends ModelWithHasStatus
{
    public static $hasStatusClassName = SurveyHasStatus::class;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return array_merge([
            [['name','status'], 'required'],
            [['name', 'status'], 'string', 'max' => 255],
            [['name'], 'string','max' => 500],
            [['options'], 'string','max' => 1024 * 10],
        ], parent::rules());
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'survey_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Survey name'),
            'status' => Yii::t('app', 'Status'),
            'options' => Yii::t('app', 'Options JSON'),
            'key' => Yii::t('app', 'Survey key'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeHints()
    {
        return [
            'key' => Yii::t('app', 'Survey key is a cross-platform multi-mode unique id for survey.'),
        ];
    }

    /**
     * @return mixed
     */
    public function getOptionsDecoded(){
        return json_decode($this->options);
    }


    /**
     * @param string $key survey uuid
     * @return static|array|null|yii\db\ActiveRecord
     */
    public static function findByKey($key)
    {
        return static::find()->andWhere("key=:key", [":key" => $key])->one();
    }





}