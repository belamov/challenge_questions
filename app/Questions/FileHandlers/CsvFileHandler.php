<?php

namespace Questions\FileHandlers;

use Questions\Exceptions\DecodingException;
use Questions\Exceptions\EncodingException;
use Throwable;

/**
 * we assume that files will be small,
 * so we will handle them in memory without problems
 *
 * if files will become too big to handle them in memory,
 * we will use some more efficient repository implementation
 * for questions, like database
 */
class CsvFileHandler extends AbstractFileHandler
{
    public function decode(string $pathToFile): array
    {
        try {
            $result = [];
            $row = 0;

            $handle = fopen($pathToFile, 'rb');

            if (!$handle) {
                throw new DecodingException("couldnt open file '$pathToFile' for read");
            }

            while (($data = fgetcsv($handle)) !== false) {
                if ($row > 0) {
                    $result[] = $data;
                }
                $row++;
            }

            fclose($handle);

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