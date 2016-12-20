<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\MyActiveRecord;
use andmemasin\surveybasemodels\Survey;
/**
 * This is the model class for a generic Respondent.
 *
 * @property int $id
 * @property int $survey_id
 * @property Survey $survey
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
        ], parent::rules());
    }


}