<?php

namespace Questions\Decoders;

use Questions\Exceptions\ParsingException;
use Throwable;

class CsvFileDecoder extends AbstractFileDecoder
{
    /**
     * @throws ParsingException
     */
    public function decode(string $pathToFile): array
    {
        try {
            //for now lets assume that csv files suppose to be small,
            //so we will manage them in memory with no problems
            // TODO: add decoding of large csv files
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
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "cant parse csv file '$pathToFile'",
                previous: $exception,
            );
        }
    }

    /**
     * @throws ParsingException
     */
    public function encode(array $data): string
    {
        try {
            $f = fopen('php://memory', 'rb+');
            fputcsv($f, ["Question text", "Created At", "Choice 1", "Choice 2", "Choice 3"]);
            foreach ($data as $item) {
                fputcsv($f, $item);
            }
            rewind($f);
            return stream_get_contents($f);
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "cant encode array to csv",
                previous: $exception,
            );
        }
    }
}