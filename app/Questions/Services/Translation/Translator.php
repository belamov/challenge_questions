<?php

namespace Questions\Services\Translation;

use Questions\Entities\Question;
use Questions\Services\Translation\Engines\TranslatorEngineInterface;

class Translator
{
    public function __construct(protected TranslatorEngineInterface $translatorEngine)
    {
    }

    /**
     * @param  array<Translatable>  $translatable
     * @return array<Question>
     */
    public function translateQuestions(array $translatable, string $language): array
    {
        return array_map(
            fn(Translatable $translatable) => $translatable->translate($this->translatorEngine, $language),
            $translatable
        );
    }
}