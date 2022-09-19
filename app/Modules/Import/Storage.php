<?php

namespace App\Modules\Import;

/**
 * Persistence layer
 */
class Storage
{
    private $importedData = [];
    private $compareFields = ['first_name', 'last_name', 'email', 'phone', 'address_line_1', 'city', 'province', 'postcode'];
    private $duplicates = 0;

    /**
     * Persists the data to storage
     *
     * @param array $mappedData
     * @return void
     */
    public function save(array $mappedData) {
        $mappedData = $this->sort($mappedData);
        foreach ($mappedData as $row) {
            $this->importRow($row);
        }
    }
    /**
     * Getter for importedData
     *
     * @return array
     */
    public function getImportedData(): array {
        return $this->importedData;
    }
    /**
     * Finds and returns updatable questions
     *
     * @param array $data
     * @return array
     */
    public function getUpdatable(array $data): array {
        $questions = [];
        foreach ($data as $name => $row) {
            if ($row->is_survey_question === true) {
                $questions[] = $name;
            }
        }
        return $questions;
    }
    /**
     * Getter for the number of duplicates
     *
     * @return integer
     */
    public function getNumberOfDuplicates(): int {
        return $this->duplicates;
    }
    /**
     * Finds a contact by specified field
     *
     * @param string $field
     * @param string $value
     * @return array|null
     */
    public function findContactByField(string $field, string $value): ?array {
        foreach ($this->importedData as $row) {
            if ($row[$field]->value == $value) {
                return $row;
            }
        }
    }
    /**
     * Checks if the row exists already and if not, it imports it. If the row does exist, it updates the question fields.
     *
     * @param array $row
     * @return void
     */
    private function importRow(array $row) {
        $contactID = $this->findContact($row);
        if ($contactID === false) {
            $this->importedData[] = $row;
            return;
        }
        $this->duplicates++;
        $this->updateQuestions($contactID, $row);
    }
    /**
     * Finds a contact. If its found, it returns the position, otherwise it returns false
     *
     * @param array $row
     * @return boolean|int
     */
    private function findContact(array $row): bool|int {
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
    /**
     * Sorts the mapped data by date
     *
     * @param array $mappedData
     * @return array
     */
    private function sort(array $mappedData): array {
        $vals = array_column($mappedData, 'date_added');
        array_multisort($vals, SORT_ASC, SORT_REGULAR, $mappedData);
        return $mappedData;
    }
    /**
     * Updates the question fields for a record
     *
     * @param integer $contactID
     * @param array $data
     * @return void
     */
    private function updateQuestions(int $contactID, array $data) {
        $questions = $this->getUpdatable($data);
        foreach($questions as $question) {
            $this->importedData[$contactID][$question] = $data[$question];
        }
    }
}