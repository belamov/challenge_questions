<?php

namespace Questions\Services\Translation;

use Questions\Services\Translation\Engines\TranslatorEngineInterface;

interface Translatable
{
    public function translate(TranslatorEngineInterface $translatorEngine, string $language): Translatable;
}