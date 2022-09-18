<?php

namespace App\Modules\Import;

use App\Modules\Import\ImportObject;

class Mapper
{
    private $invalidRows = [];
    private $mappedData = [];
    private $csv = [];
    private $header = [];
    private $incompleteContacts = 0;

    public function __construct(array $csv, $map) {
        $this->csv = $csv;
        $this->map = $map->getMap();
    }

    public function getNumFields(): int {
        return sizeof($this->map);
    }

    public function getTotalRows(): int {
        return sizeof($this->csv);
    }

    public function getTotalIncompleteContacts(): int {
        return $this->incompleteContacts;
    }

    public function getInvalidRows(): array {
        return $this->invalidRows;
    }

    public function getMappedData(): array {
        return $this->mappedData;
    }

    public function build() {
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
    }

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

    private function mapRow($row): array {
        $mappedRow = [];

        for($i = 0; $i < count($row); $i++) {
            if ($this->validField($this->header[$i], $row[$i])) {
                $fieldName = $this->header[$i];
                $mappedRow[$fieldName] =
                    new ImportObject(
                        $row[$i],
                        $this->map[$fieldName]['required'],
                        $this->map[$fieldName]['is_survey_question']
                    );
            }
            else {
                $this->incompleteContacts++;
                throw new \Exception($this->map[$i]['field']);
            }
        }
        return $mappedRow;
    }

    private function validField($fieldName, $data): bool {
        if ($this->map[$fieldName]['required'] === false) {
            return true;
        }
        if (! empty($data)) {
            return true;
        }
        return false;
    }

}