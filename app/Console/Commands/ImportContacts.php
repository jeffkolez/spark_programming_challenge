<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Import\Mapper;
use App\Modules\Import\Storage;
use App\Modules\Import\CsvMap;
use App\Modules\Import\Report;

class ImportContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:import {name} {--report=} {--format=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV file';


    private function importCSV($fileName) {
        $csv = [];
        $fileHandle = fopen($fileName, 'r');
        while (!feof($fileHandle)) {
            $row = fgetcsv($fileHandle, 4096);
            if (empty($row)) {
                continue;
            }
            $csv[] = $row;
        }
        fclose($fileHandle);
        return $csv;
    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $csv = $this->importCSV(
            $this->argument('name')
        );
        $mapper = new Mapper($csv, new CsvMap());
        $storage = new Storage();

        $mapper->build();
        $storage->save($mapper->getMappedData());

        $report = new Report($storage, $mapper);

        switch($this->option('report')) {
            case 'imported':
                $data = $report->formatImportedData();
                break;
            case 'invalid':
                $data = $report->formatInvalidRows();
                break;
            default:
                $data = $report->getImportReport();
        }

        switch($this->option('format')) {
            case 'json':
                echo json_encode($data);
                break;
            default:
                print_r($data);
        }

        return 0;
    }
}
