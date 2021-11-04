<?php

return [
    /*
  |--------------------------------------------------------------------------
  | file path
  |--------------------------------------------------------------------------
  |
  | where to store file database.
  |
  */
    'file_path' => env('FILE_PATH', '/app/questions.csv'),

    /*
   |--------------------------------------------------------------------------
   | file format
   |--------------------------------------------------------------------------
   |
   | which files to use as database.
   | available values: csv, json
   |
   */
    'file_format' => env('FILE_FORMAT', 'csv'),
];