<?php

namespace App\Modules\Import;

class Storage
{
    private $importedData = null;
    private $compareFields = ['first_name', 'last_name', 'email', 'phone', 'address_line_1', 'city', 'province', 'postcode'];
    private $duplicates = 0;


    public function save(array $mappedData) {
        $mappedData = $this->sort($mappedData);
        foreach ($mappedData as $row) {
            $this->importRow($row);
        }
    }

    public function getImportedData() {
        return $this->importedData;
    }

    public function getUpdatable($data) {
        $questions = [];
        foreach ($data as $name => $row) {
            if ($row->is_survey_question === true) {
                $questions[] = $name;
            }
        }
        return $questions;
    }

    public function getNumberOfDuplicates() {
        return $this->duplicates;
    }

    public function findContactByField($field, $value): ?array {
        foreach ($this->importedData as $row) {
            if ($row[$field]->value == $value) {
                return $row;
            }
        }
    }

    private function importRow(array $row) {
        $contactID = $this->findContact($row);
        if ($contactID === false) {
            $this->createRow($row);
        }
        else {
            $this->duplicates++;
            $this->updateQuestions($contactID, $row);
        }
    }

    private function createRow(array $row) {
        $this->importedData[] = $row;
    }

    private function findContact(array $row) {
        if (empty($this->importedData)) {
            return false;
        }
        $contactID = 0;
        foreach ($this->importedData as $data) {
            $found = true;
            foreach ($this->compareFields as $comparisonField) {
                if ($data[$comparisonField]->value !== $row[$comparisonField]->value) {
                    $found = false;
                    break;
                }
            }
            if ($found) {
                return $contactID;
            }
            $contactID++;
        }
        return false;
    }

    private function sort(array $mappedData): array {
        $vals = array_column($mappedData, 'date_added');
        array_multisort($vals, SORT_ASC, SORT_REGULAR, $mappedData);
        return $mappedData;
    }

    private function updateQuestions(int $contactID, array $data) {
        $questions = $this->getUpdatable($data);
        foreach($questions as $question) {
            $this->importedData[$contactID][$question] = $data[$question];
        }
    }
}