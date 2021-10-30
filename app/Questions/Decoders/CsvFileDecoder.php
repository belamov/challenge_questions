<?php

namespace Questions\Decoders;

class CsvFileDecoder extends AbstractFileDecoder
{
    public function decode(string $pathToFile): array
    {
        //for now lets assume that csv files suppose to be small,
        //so we will manage them in memory with no problems
        // TODO: add decoding of large csv files
        // TODO: handling headings row?
        $result = [];
        $row = 0;
        if (($handle = fopen($pathToFile, 'rb')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                if ($row !== 0) {
                    $result[] = $data;
                }
                $row++;
            }
            fclose($handle);
        }
        return $result;
    }
}