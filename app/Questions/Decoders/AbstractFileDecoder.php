<?php

namespace Questions\Decoders;

use Questions\Exceptions\DecodingException;
use Questions\Exceptions\EncodingException;

abstract class AbstractFileDecoder
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