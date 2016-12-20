<?php

namespace app\modules\surveybasemodels;

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
 *
 */
class Survey extends MyActiveRecord
{

}