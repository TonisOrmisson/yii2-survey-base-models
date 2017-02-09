<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\MyActiveRecord;

/**
 * This is the model class for table "status".
 *
 * @property string $status
 *
 */
class Status extends MyActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge([
            [['status'], 'required'],
            [['status'], 'string', 'max' => 32],
            [['status'], 'unique'],
            [['description'], 'string', 'max' => 500],
        ],  parent::rules());
    }


}
