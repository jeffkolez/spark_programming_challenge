<?php

namespace App\Modules\Import;

class Importer
{
    private $mapper = null;
    private $mappedData = null;

    public function __construct($mapper) {
        $this->mapper = $mapper;
    }

    public function process(): bool {
        $this->mapper->build();
        $this->mappedData = $this->mapper->getMappedData();
        return true;
    }

    public function getMappedData(): array {
        return $this->mappedData;
    }

}