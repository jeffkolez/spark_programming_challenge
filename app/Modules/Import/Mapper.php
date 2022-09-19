<?php

namespace App\Modules\Import;

use App\Modules\Import\ImportObject;
use App\Modules\Import\CsvMap;
/**
 * Mapper class for import
 */
class Mapper
{
    private $invalidRows = [];
    private $mappedData = [];
    private $csv = [];
    private $header = [];

    /**
     * Constructor
     *
     * @param array $csv
     * @param CsvMap $map
     */
    public function __construct(array $csv, CsvMap $map) {
        $this->csv = $csv;
        $this->map = $map->getMap();
    }
    /**
     * Suze of the map
     *
     * @return integer
     */
    public function getNumFields(): int {
        return sizeof($this->map);
    }
    /**
     * Total number of rows in the import
     *
     * @return integer
     */
    public function getTotalRows(): int {
        return sizeof($this->csv);
    }
    /**
     * Getter for the number of invalid rows
     *
     * @return array
     */
    public function getInvalidRows(): array {
        return $this->invalidRows;
    }
    /**
     * Getter for the mapped data
     *
     * @return array
     */
    public function getMappedData(): array {
        return $this->mappedData;
    }
    /**
     * Builds a relational array of the imported data. Throws exception if build fails
     *
     * @return bool
     * @throws Exception
     */
    public function build(): bool {
        $row = 0;
        $this->validateHeader();
        $this->mappedData = [];
        for ($i = 0; $i < sizeof($this->csv); $i++) {
            if (! is_array($this->csv[$i])) {
                continue;
            }
            try {
                $this->mappedData[] = $this->mapRow($this->csv[$i]);
            }
            catch(\Exception $e) {
                $this->invalidRows[$i] = $e->getMessage();
            }
            $row++;
        }
        return true;
    }
    /**
     * Validates the header. Throws an exception if validation fails
     *
     * @throws Exception
     * @return boolean
     */
    private function validateHeader(): bool {
        if (sizeof($this->csv) == 0) {
            throw new \Exception('Missing header');
        }
        $this->header = array_shift($this->csv);
        if ($this->getNumFields() != sizeof($this->header)) {
            throw new \Exception('Number of fields does not match expected');
        }
        $mappedKeys = array_keys($this->map);
        $keys = array_diff($mappedKeys, $this->header);
        if (! empty($keys)) {
            throw new \Exception('Missing ' . implode($keys));
        }
        return true;
    }
    /**
     * Maps a row of data
     *
     * @param array $row
     * @return array
     */
    private function mapRow(array $row): array {
        $mappedRow = [];

        for($i = 0; $i < count($row); $i++) {
            if ((sizeof($row) <= 1) && (empty($row[0]))){
                continue;
            }
            if (! $this->validField($this->header[$i], $row[$i])) {
                throw new \Exception($this->header[$i]);
            }
            $fieldName = $this->header[$i];
            $mappedRow[$fieldName] =
                new ImportObject(
                    $row[$i],
                    $this->map[$fieldName]['required'],
                    $this->map[$fieldName]['is_survey_question']
                );
        }
        return $mappedRow;
    }
    /**
     * Validates field
     *
     * @param string $fieldName
     * @param string $data
     * @return boolean
     */
    private function validField(string $fieldName, string $data): bool {
        if ($this->map[$fieldName]['required'] === false) {
            return true;
        }
        if (! empty($data)) {
            return true;
        }
        return false;
    }

}