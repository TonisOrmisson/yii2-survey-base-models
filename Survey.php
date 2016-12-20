<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\MyActiveRecord;

/**
 * This is the model class for a generic Survey. This describes common
 * parameters all Survey models must have regardless of the methodology.
 * All Surveys in multiple modes (CATI, CAPI, PANEL, WEB etc.) must extend
 * this class.
 *
 * @property int $id
 * @property string $key
 * @property string $name
 * @property string $options The options as json string. Contains the
 * Collector authentication information (or any other info)
 *
 */
class Survey extends MyActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['name'], 'required'],
            [['name'], 'string','max' => 500],
        ], parent::rules());
    }

}