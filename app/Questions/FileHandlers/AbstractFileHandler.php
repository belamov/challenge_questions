<?php

namespace Questions\FileHandlers;

use Questions\Exceptions\DecodingException;
use Questions\Exceptions\EncodingException;

abstract class AbstractFileHandler
{
    /**
     * @throws DecodingException
     */
    abstract public function decode(string $pathToFile): array;


    /**
     * @throws EncodingException
     */
    abstract public function encode(array $data): string;
}