<?php

namespace Questions\Services\Translation;

use Questions\Services\Translation\Engines\TranslatorEngineInterface;

class Translator
{
    public function __construct(protected TranslatorEngineInterface $translatorEngine)
    {
    }

    /**
     * @param  array<Translatable>  $translatable
     * @return array<Translatable>
     */
    public function translateItems(array $translatable, string $language): array
    {
        return array_map(
            fn(Translatable $translatable) => $translatable->translate($this->translatorEngine, $language),
            $translatable
        );
    }
}