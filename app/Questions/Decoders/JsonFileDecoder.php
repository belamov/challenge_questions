<?php

namespace Questions\Decoders;

use Questions\Exceptions\ParsingException;

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
        } catch (\Throwable $exception) {
            throw new ParsingException(
                message: "cant parse file '$pathToFile'",
                previous: $exception,
            );
        }
    }
}