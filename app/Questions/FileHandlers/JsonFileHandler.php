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
class JsonFileHandler extends AbstractFileHandler
{
    public function decode(string $pathToFile): array
    {
        try {
            $json = file_get_contents($pathToFile);

            if (!$json) {
                throw new DecodingException("file '$pathToFile' not found");
            }

            return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new DecodingException(
                message: "cant read json file '$pathToFile', it seems invalid",
                previous: $exception,
            );
        }
    }

    public function encode(array $data): string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new EncodingException(
                message: 'cannot encode array to json',
                previous: $exception
            );
        }
    }
}