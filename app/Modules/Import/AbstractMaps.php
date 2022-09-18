<?php

namespace App\Modules\Import;

abstract class AbstractMaps
{

    public function getMap() {
        return $this->map;
    }
}