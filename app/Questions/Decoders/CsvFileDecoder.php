<?php

namespace Questions\Decoders;

use Questions\Exceptions\DecodingException;
use Questions\Exceptions\EncodingException;
use Throwable;

class CsvFileDecoder extends AbstractFileDecoder
{
    public function decode(string $pathToFile): array
    {
        //for now lets assume that csv files suppose to be small,
        //so we will manage them in memory with no problems
        try {
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
            throw new DecodingException(
                message: "cant decode csv file '$pathToFile'",
                previous: $exception,
            );
        }
    }

    /**
     * @throws EncodingException
     */
    public function encode(array $data): string
    {
        //for now lets assume that csv files suppose to be small,
        //so we will manage them in memory with no problems
        try {
            $f = fopen('php://memory', 'rb+');

            if (!$f) {
                throw new EncodingException('couldnt create file in memory for encoding csv');
            }

            fputcsv($f, ["Question text", "Created At", "Choice 1", "Choice 2", "Choice 3"]);
            foreach ($data as $item) {
                fputcsv($f, $item);
            }
            rewind($f);

            $result = stream_get_contents($f);

            if (!$result) {
                throw new EncodingException('couldnt read from in-memory file for encoding csv');
            }

            return $result;
        } catch (Throwable $exception) {
            throw new EncodingException(
                message: "cant encode array to csv",
                previous: $exception,
            );
        }
    }
}