<?php

namespace App\Modules\Import;

use App\Modules\Import\Storage;
use App\Modules\Import\Mapper;

class Report
{
    private $storage = null;
    private $mapper = null;
    /*total number of contacts
    number of valid contacts
    number of duplicate contacts (rows for which the contact info is the same as another row)
    number of incomplete contacts (rows for which there is no value for one or more headers, excluding Q&A columns)
    */

    public function __construct(Storage $storage, Mapper $mapper) {
        $this->storage = $storage;
        $this->mapper = $mapper;
    }

    public function getReport() {
        return [
            'duplicates' => $this->storage->getNumberOfDuplicates(),
            'total_rows' => $this->mapper->getTotalRows(),
            'total_valid_rows' => sizeof($this->storage->getImportedData()),
            'total_incomplete' => $this->mapper->getTotalIncompleteContacts()
        ];
    }


}