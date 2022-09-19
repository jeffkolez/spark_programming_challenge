<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Modules\Import\Mapper;
use App\Modules\Import\CsvMap;

class ImportHeaderTest extends TestCase
{
    public function test_it_should_have_valid_header()
    {
        $csv = "first_name,last_name,email,phone,address_line_1,city,province,country_name,postcode,date_added,how_did_you_hear_about_us,what_is_your_budget,what_is_your_favourite_color\n";

        $csvArray = [];
        $data = explode("\n", $csv);
        foreach ($data as $row) {
            $csvArray[] = str_getcsv($row);
        }

        $mapper = new Mapper($csvArray, new CsvMap());

        $this->assertTrue($mapper->build());
    }
    public function test_it_should_have_valid_header_in_different_order()
    {
        $csv = "last_name,first_name,email,phone,address_line_1,city,province,country_name,postcode,date_added,how_did_you_hear_about_us,what_is_your_budget,what_is_your_favourite_color\n";

        $csvArray = [];
        $data = explode("\n", $csv);
        foreach ($data as $row) {
            $csvArray[] = str_getcsv($row);
        }

        $mapper = new Mapper($csvArray, new CsvMap());

        $this->assertTrue($mapper->build());
    }
    public function test_it_should_have_empty_header()
    {
        $csvArray = [];

        $mapper = new Mapper($csvArray, new CsvMap());

        try {
            $mapper->build();
        }
        catch (\Exception $e) {
            $this->assertEquals('Missing header', $e->getMessage());
        }
    }
    public function test_header_should_have_wrong_number_of_columns()
    {
        $csv = "first_name,last_name,email,phone,address_line_1,city,province,country_name,postcode,date_added,how_did_you_hear_about_us,what_is_your_budget\n";

        $csvArray = [];
        $data = explode("\n", $csv);
        foreach ($data as $row) {
            $csvArray[] = str_getcsv($row);
        }

        $mapper = new Mapper($csvArray, new CsvMap());

        try {
            $mapper->build();
        }
        catch (\Exception $e) {
            $this->assertEquals('Number of fields does not match expected', $e->getMessage());
        }
    }
    public function test_header_should_have_incorrect_header_name()
    {
        $csv = "first_name,last_name,email,phone,address_line,city,province,country_name,postcode,date_added,how_did_you_hear_about_us,what_is_your_budget,what_is_your_favourite_color\n";

        $csvArray = [];
        $data = explode("\n", $csv);
        foreach ($data as $row) {
            $csvArray[] = str_getcsv($row);
        }

        $mapper = new Mapper($csvArray, new CsvMap());

        try {
            $mapper->build();
        }
        catch (\Exception $e) {
            $this->assertEquals('Missing address_line_1', $e->getMessage());
        }
    }
}
