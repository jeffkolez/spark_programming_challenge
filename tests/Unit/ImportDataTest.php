<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Modules\Import\Mapper;
use App\Modules\Import\Storage;
use App\Modules\Import\CsvMap;
use App\Modules\Import\Report;

class ImportDataTest extends TestCase
{
    public function test_it_should_have_one_record()
    {
        $csv = "first_name,last_name,email,phone,address_line_1,city,province,country_name,postcode,date_added,how_did_you_hear_about_us,what_is_your_budget,what_is_your_favourite_color\n";
        $csv .= "Glenda,Rosas,glendarrosas@dodgit.com,613-937-0694,326 Donato Ridges,Rimbey,AB,Canada,T0C 2J0,2015-02-24 10:54:02 UTC,Newspaper,$200-$299,Blue";

        $csvArray = [];
        $data = explode("\n", $csv);
        foreach ($data as $row) {
            $csvArray[] = str_getcsv($row);
        }

        $storage = new Storage();
        $mapper = new Mapper($csvArray, new CsvMap());

        $mapper->build();
        $storage->save($mapper->getMappedData());
        $reportObj = new Report($storage, $mapper);
        $report = $reportObj->getImportReport();
        $this->assertEquals($report['total_valid_rows'], 1);
        $this->assertEquals($report['duplicates'], 0);
        $this->assertEquals($report['total_rows'], 1);
        $this->assertEquals($report['total_incomplete'], 0);
    }
    public function test_it_should_have_two_records()
    {
        $csv = "first_name,last_name,email,phone,address_line_1,city,province,country_name,postcode,date_added,how_did_you_hear_about_us,what_is_your_budget,what_is_your_favourite_color\n";
        $csv .= "Margaret,Padberg,magpads@gmail.com,(599) 684-9711,325 Donato Ridges,Veumhaven,OH,USA,51234,2013-09-26 11:06:00 UTC,TV,$200-$299,Orange\n";
        $csv .= "Woodrow,Brown,woodrowdbrown@mailinator.com,905-338-1135,479 Ari Ridges,Terrace,BC,Canada,V8G 1S2,2013-09-10 08:30:19 UTC,Newspaper,$200-$299,Green\n";
        $csv .= "Margaret,Padberg,magpads@gmail.com,(599) 684-9711,325 Donato Ridges,Veumhaven,OH,USA,51234,2015-05-06 08:32:21 UTC,TV,$200-$299,Orange";

        $csvArray = [];
        $data = explode("\n", $csv);
        foreach ($data as $row) {
            $csvArray[] = str_getcsv($row);
        }
        $storage = new Storage();
        $mapper = new Mapper($csvArray, new CsvMap());

        $mapper->build();
        $storage->save($mapper->getMappedData());
        $reportObj = new Report($storage, $mapper);
        $report = $reportObj->getImportReport();
        $this->assertEquals($report['total_valid_rows'], 2);
        $this->assertEquals($report['duplicates'], 1);
        $this->assertEquals($report['total_rows'], 3);
        $this->assertEquals($report['total_incomplete'], 0);
    }
    public function test_it_should_handle_duplicate_rows()
    {
        $csv = "first_name,last_name,email,phone,address_line_1,city,province,country_name,postcode,date_added,how_did_you_hear_about_us,what_is_your_budget,what_is_your_favourite_color\n";
        $csv .= "Margaret,Padberg,magpads@gmail.com,(599) 684-9711,325 Donato Ridges,Veumhaven,OH,USA,51234,2013-09-26 11:06:00 UTC,Google,$100-$199,Orange\n";
        $csv .= "Margaret,Padberg,magpads@gmail.com,(599) 684-9711,325 Donato Ridges,Veumhaven,OH,USA,51234,2015-05-06 08:32:21 UTC,TV,$200-$299,Purple";

        $csvArray = [];
        $data = explode("\n", $csv);
        foreach ($data as $row) {
            $csvArray[] = str_getcsv($row);
        }

        $storage = new Storage();
        $mapper = new Mapper($csvArray, new CsvMap());

        $mapper->build();
        $storage->save($mapper->getMappedData());
        $reportObj = new Report($storage, $mapper);
        $report = $reportObj->getImportReport();
        $this->assertEquals($report['total_valid_rows'], 1);
        $this->assertEquals($report['duplicates'], 1);
        $this->assertEquals($report['total_rows'], 2);
        $this->assertEquals($report['total_incomplete'], 0);

        $margaret = $storage->findContactByField('first_name', 'Margaret');
        $this->assertEquals($margaret['how_did_you_hear_about_us']->value, 'TV');
        $this->assertEquals($margaret['what_is_your_budget']->value, '$200-$299');
        $this->assertEquals($margaret['what_is_your_favourite_color']->value, 'Purple');
    }
    public function test_it_should_update_questions_and_invalid_row()
    {
        $csv = "first_name,last_name,email,phone,address_line_1,city,province,country_name,postcode,date_added,how_did_you_hear_about_us,what_is_your_budget,what_is_your_favourite_color\n";
        $csv .= "Margaret,Padberg,magpads@gmail.com,(599) 684-9711,325 Donato Ridges,Veumhaven,OH,USA,51234,2013-09-26 11:06:00 UTC,TV,$200-$299,Orange\n";
        $csv .= "Margaret,Padberg,magpads@gmail.com,(599) 684-9711,325 Donato Ridges,Veumhaven,OH,USA,51234,2015-05-06 08:32:21 UTC,TV,$200-$299,Purple\n";
        $csv .= "Woodrow,Brown,woodrowdbrown@mailinator.com,905-338-1135,479 Ari Ridges,Terrace,BC,Canada,V8G 1S2,2014-06-16 13:29:08 UTC,Google,$200-$299,Green\n";
        $csv .= "Woodrow,Brown,woodrowdbrown@mailinator.com,905-338-1135,479 Ari Ridges,Terrace,BC,Canada,V8G 1S2,2013-09-10 08:30:19 UTC,Newspaper,$200-$299,Green\n";
        $csv .= "Teri,Battle,,,327 Donato Ridges,Oshawa,ON,Canada,L1G 6Z8,2013-07-19 06:33:18 UTC,Newspaper,$200-$299,Blue";

        $csvArray = [];
        $data = explode("\n", $csv);
        foreach ($data as $row) {
            $csvArray[] = str_getcsv($row);
        }

        $storage = new Storage();
        $mapper = new Mapper($csvArray, new CsvMap());

        $mapper->build();
        $storage->save($mapper->getMappedData());
        $reportObj = new Report($storage, $mapper);
        $report = $reportObj->getImportReport();
        $this->assertEquals($report['total_valid_rows'], 2);
        $this->assertEquals($report['duplicates'], 2);
        $this->assertEquals($report['total_rows'], 5);
        $this->assertEquals($report['total_incomplete'], 1);

        $woodrow = $storage->findContactByField('first_name', 'Woodrow');

        $this->assertEquals($woodrow['how_did_you_hear_about_us']->value, 'Google');
        $this->assertEquals($woodrow['what_is_your_budget']->value, '$200-$299');
        $this->assertEquals($woodrow['what_is_your_favourite_color']->value, 'Green');

        $margaret = $storage->findContactByField('first_name', 'Margaret');
        $this->assertEquals($margaret['how_did_you_hear_about_us']->value, 'TV');
        $this->assertEquals($margaret['what_is_your_budget']->value, '$200-$299');
        $this->assertEquals($margaret['what_is_your_favourite_color']->value, 'Purple');
    }
    public function test_it_should_handle_different_header_order()
    {
        $csv = "last_name,first_name,email,phone,address_line_1,city,province,country_name,postcode,date_added,how_did_you_hear_about_us,what_is_your_budget,what_is_your_favourite_color\n";
        $csv .= "Padberg,Margaret,magpads@gmail.com,(599) 684-9711,325 Donato Ridges,Veumhaven,OH,USA,51234,2013-09-26 11:06:00 UTC,TV,$200-$299,Orange\n";
        $csv .= "Padberg,Margaret,magpads@gmail.com,(599) 684-9711,325 Donato Ridges,Veumhaven,OH,USA,51234,2015-05-06 08:32:21 UTC,TV,$200-$299,Orange";

        $csvArray = [];
        $data = explode("\n", $csv);
        foreach ($data as $row) {
            $csvArray[] = str_getcsv($row);
        }

        $storage = new Storage();
        $mapper = new Mapper($csvArray, new CsvMap());

        $mapper->build();
        $storage->save($mapper->getMappedData());
        $reportObj = new Report($storage, $mapper);
        $report = $reportObj->getImportReport();
        $this->assertEquals($report['total_valid_rows'], 1);
        $this->assertEquals($report['duplicates'], 1);
        $this->assertEquals($report['total_rows'], 2);
        $this->assertEquals($report['total_incomplete'], 0);

        $margaret = $storage->findContactByField('first_name', 'Margaret');
        $this->assertEquals($margaret['how_did_you_hear_about_us']->value, 'TV');
        $this->assertEquals($margaret['what_is_your_budget']->value, '$200-$299');
        $this->assertEquals($margaret['what_is_your_favourite_color']->value, 'Orange');
    }
}
