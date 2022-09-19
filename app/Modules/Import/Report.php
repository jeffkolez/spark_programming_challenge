<?php

namespace App\Modules\Import;

use App\Modules\Import\Storage;
use App\Modules\Import\Mapper;

/**
 * Report class
 */
class Report
{
    private $storage = null;
    private $mapper = null;
    /**
     * Constructor
     *
     * @param Storage $storage
     * @param Mapper $mapper
     */
    public function __construct(Storage $storage, Mapper $mapper) {
        $this->storage = $storage;
        $this->mapper = $mapper;
    }
    /**
     * Fetches and formats the report for a given import
     *
     * @return array
     */
    public function getImportReport(): array {
        return [
            'duplicates' => $this->storage->getNumberOfDuplicates(),
            'total_rows' => $this->mapper->getTotalRows(),
            'total_valid_rows' => sizeof($this->storage->getImportedData()),
            'total_incomplete' => sizeof($this->mapper->getInvalidRows())
        ];
    }

    public function formatImportedData(): array {
        $data = [];
        foreach($this->storage->getImportedData() as $row) {
            $rowData = [];
            foreach($row as $key => $row) {
                $rowData[$key] = $row->value;
            }
            $data[] = $rowData;
        }
        return $data;
    }

    public function formatInvalidRows(): array {
        $data = [];
        foreach($this->mapper->getInvalidRows() as $key => $field) {
            $data[] = "Row {$key} missing value for {$field}";
        }
        return $data;
    }


}