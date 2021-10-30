<?php

namespace Questions\Decoders;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

abstract class AbstractFileDecoder
{
    /**
     * @throws FileNotFoundException
     */
    public function checkFileExists(string $pathToFile): void
    {
        if (!file_exists($pathToFile)) {
            throw new FileNotFoundException("file '$pathToFile' not found");
        }
    }

    abstract public function decode(string $pathToFile): array;

    abstract public function encode(array $data): string;
}