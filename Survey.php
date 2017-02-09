<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\ModelWithHasStatus;
use andmemasin\survey\api\Status;

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
 * @property string $options The options as json string. Contains the
 * Collector authentication information (or any other info)
 *
 */
class Survey extends ModelWithHasStatus
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->hasStatusClassName = SurveyHasStatus::className();
        parent::init();
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['name'], 'required'],
            [['name', 'status'], 'string', 'max' => 255],
            [['name'], 'string','max' => 500],
            [['options'], 'string','max' => 1024 * 10],
        ], parent::rules());
    }

    public function getOptionsDecoded(){
        return json_decode($this->options);
    }

    /**
     * Check whether survey key is locked.
     * A new key may be assigned to survey only when the key is newly created
     * and no any further statuses have not been assigned to it
     *
     */
    public function isKeyLocked(){
        return in_array($this->currentStatus->status,array_keys(Status::getLockedStatuses()));
    }

}