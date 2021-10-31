<?php

namespace Questions\FileHandlers;

use Questions\Exceptions\DecodingException;
use Questions\Exceptions\EncodingException;
use Throwable;

class JsonFileHandler extends AbstractFileHandler
{
    public function decode(string $pathToFile): array
    {
        //for now lets assume that json files suppose to be small,
        //so we will manage them in memory with no problems
        try {
            $json = file_get_contents($pathToFile);

            if (!$json) {
                throw new DecodingException("file '$pathToFile' not found");
            }

            return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new DecodingException(
                message: "cant read json file '$pathToFile', it seems invalid",
                previous: $exception,
            );
        }
    }

    public function encode(array $data): string
    {
        //for now lets assume that json files suppose to be small,
        //so we will manage them in memory with no problems
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