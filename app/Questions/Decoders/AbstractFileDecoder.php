<?php

namespace Questions\Decoders;

use Questions\Exceptions\ParsingException;

abstract class AbstractFileDecoder
{
    /**
     * @throws ParsingException
     */
    public function checkFileExists(string $pathToFile): void
    {
        if (!file_exists($pathToFile)) {
            throw new ParsingException("file '$pathToFile' not found");
        }
    }

    abstract public function decode(string $pathToFile): array;

    abstract public function encode(array $data): string;
}