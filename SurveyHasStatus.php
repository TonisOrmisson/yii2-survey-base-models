<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\HasStatusModel;
use andmemasin\survey\Status;

/**
 * This is the model class for table "survey_has_status".
 *
 * @property integer $survey_has_status_id
 * @property integer $survey_id
 * @property string $status
 *
 */
class SurveyHasStatus extends HasStatusModel
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->parentClassName = Survey::className();
        $this->childClassName = Status::className();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'survey_has_status';
    }


    /**
     * @param Survey $survey
     * @param string $status if not set Status will be taken from Email
     */
    public static function create($survey,$status = null){
        $model = new static();
        $model->survey_id = $survey->getPrimaryKey();
        $model->status =$survey->status;
        if($status){
            $model->status =$status;
        }
        return $model->save();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['survey_id','status'], 'required'],
            [['status'], 'string', 'max' => 32],
            [['survey_id'], 'integer'],
        ],  parent::rules());
    }
}
