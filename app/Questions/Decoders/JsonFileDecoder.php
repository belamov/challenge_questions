<?php

namespace Questions\Decoders;

use Questions\Exceptions\ParsingException;
use Throwable;

class JsonFileDecoder extends AbstractFileDecoder
{
    /**
     * @throws ParsingException
     */
    public function decode(string $pathToFile): array
    {
        //for now lets assume that json files suppose to be small,
        //so we will manage them in memory with no problems
        // TODO: add decoding of large json files
        try {
            return json_decode(file_get_contents($pathToFile), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "cant parse json file '$pathToFile'",
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
            return json_encode($data, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: 'cannot encode array to json',
                previous: $exception
            );
        }
    }
}