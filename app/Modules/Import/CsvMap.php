<?php

namespace App\Modules\Import;

use App\Modules\Import\AbstractMaps;

class CsvMap extends AbstractMaps
{
    protected $map = [
        'first_name' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'last_name' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'email' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'phone' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'address_line_1' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'city' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'province' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'country_name' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'postcode' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'date_added' => [
            'required' => true,
            'is_survey_question' => false
        ],
        'how_did_you_hear_about_us' => [
            'required' => false,
            'is_survey_question' => true
        ],
        'what_is_your_budget' => [
            'required' => false,
            'is_survey_question' => true
        ],
        'what_is_your_favourite_color' => [
            'required' => false,
            'is_survey_question' => true
        ]
    ];
}