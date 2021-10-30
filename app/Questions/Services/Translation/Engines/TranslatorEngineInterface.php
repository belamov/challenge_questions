<?php

namespace Questions\Services\Translation\Engines;

interface TranslatorEngineInterface
{
    public function translate(array $sentences, string $language): array;
}