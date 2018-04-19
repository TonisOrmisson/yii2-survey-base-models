<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\ModelWithHasStatus;
use andmemasin\collector\models\Collector;
use yii;
use andmemasin\collector\models\CollectorInterface;

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
 * @property integer $collector_id
 * @property string $options The options as json string. Contains the
 * Collector authentication information (or any other info)
 *
 * @property Collector $collector
 *
 */
class Survey extends ModelWithHasStatus
{
    public $hasStatusClassName = SurveyHasStatus::class;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['name'], 'required'],
            [['name', 'status'], 'string', 'max' => 255],
            [['name'], 'string','max' => 500],
            [['collector_id'], 'integer'],
            [['options'], 'string','max' => 1024 * 10],
        ], parent::rules());
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'survey_id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Survey name'),
            'status' => Yii::t('app', 'Status'),
            'collector_id' => Yii::t('app', 'Data collection interface'),
            'options' => Yii::t('app', 'Options JSON'),
            'key' => Yii::t('app', 'Survey key'),
        ];
    }
    public function attributeHints()
    {
        return [
            'collector_id' => Yii::t('app', 'The data collection interface allows API access to collected data to identify status or respondents in real time. E.g. whether respondent has already answered or not.'),
            'key' => Yii::t('app', 'Survey key is a cross-platform multi-mode unique id for survey.'),
        ];
    }

    public function getOptionsDecoded(){
        return json_decode($this->options);
    }


    /**
     * @return CollectorInterface
     * @throws yii\base\InvalidConfigException
     */
    public function getCollector()
    {
        $model = Collector::findOne($this->collector_id);
        if($model){

            /** @var Collector $collector */
            $collector = Yii::createObject([
                'class' => $model->getClassName(),
                'survey'=>$this,
                'collector_id' => $this->collector_id,
                'type'=>$model->type,
            ]);
            return $collector;
        }
        return null;
    }

}