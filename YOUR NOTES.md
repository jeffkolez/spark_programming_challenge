Please leave any notes you have here. It should include:

- Dependencies and how to install them
- Instructions for running the code
- A summary of how the code works, and the expected outcome

Depends on PHP8, [Composer](https://getcomposer.org), and Laravel.

To install, run `composer install` from the command line inside the exercise directory.

Directions: `php artisan command:import <filename to import> --report=<type> --format=<type>`

The report parameter is optional. The options are:

* `imported` -- Displays the list of imported records
* `invalid` -- Displays the list of row numbers that are invalid and the reason they weren't imported
* Default -- Displays stats for the import

In this exercise, duplicate rows are considered valid however they will overwrite the survey questions.

```
jeff@JeffBook-Pro exercise % php artisan command:import contacts.csv
Array
(
    [duplicates] => 4
    [total_rows] => 33
    [total_valid_rows] => 26
    [total_incomplete] => 3
)
```

There's also an optional parameter `format` which exports as JSON because everyone loves JSON:

```
jeff@JeffBook-Pro exercise % php artisan command:import contacts.csv --format=json
{"duplicates":4,"total_rows":33,"total_valid_rows":26,"total_incomplete":3}%   
```

To run unit tests, run `phpunit`

```
jeff@JeffBook-Pro exercise % phpunit 
PHPUnit 9.5.10 by Sebastian Bergmann and contributors.

...........                                                       11 / 11 (100%)

Time: 00:00.112, Memory: 34.00 MB

OK (11 tests, 38 assertions)
```

The unit tests are in `tests\Unit\*.php`

The `Mapper` object is responsible for validating and massaging the data into a format that can be easily saved. It also keeps track of invalid rows.

`CsvMap` contains metadata and the required fields for the map and tells the `Mapper` how to map its data. If any fields are changed, this should be the only class you would need to touch. In production, I'd store this in the database.

`Storage` is similar to a database. It holds the massaged data searches and updates survey questions.

`Report` builds and formats the reports after the import.
