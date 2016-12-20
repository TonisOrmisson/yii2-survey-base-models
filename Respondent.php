<?php

namespace andmemasin\surveybasemodels;

use andmemasin\myabstract\MyActiveRecord;
use andmemasin\surveybasemodels\Survey;
use andmemasin\myabstract\traits\ModuleTrait;
/**
 * This is the model class for a generic Respondent.
 *
 * @property int $id
 * @property Survey $survey
 *
 */
class Respondent extends MyActiveRecord
{
    use ModuleTrait;

}