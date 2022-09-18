<?php

namespace App\Modules\Import;

class ImportObject
{
    public $value = null;
    public $is_survey_question = null;
    public $is_required = null;

    public function __construct($value, $is_required, $is_survey_question) {
        $this->value = $value;
        $this->is_required = $is_required;
        $this->is_survey_question = $is_survey_question;
    }

}
