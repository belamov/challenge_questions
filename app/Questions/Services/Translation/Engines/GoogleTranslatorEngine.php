<?php

namespace Questions\Services\Translation\Engines;

use Questions\Exceptions\TranslationException;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Throwable;

class GoogleTranslatorEngine implements TranslatorEngineInterface
{
    private GoogleTranslate $translator;

    public function __construct()
    {
        $this->translator = new GoogleTranslate();
    }

    /**
     * @throws TranslationException
     */
    public function translate(array $sentences, string $language): array
    {
        try {
            $separator = '%%%';

            $textToTranslate = implode($separator, $sentences);

            $translatedText = $this->translator->setSource()->setTarget($language)->translate($textToTranslate);

            $translatedItems = explode($separator, $translatedText);

            $translatedItems = array_map(static fn(string $text) => trim($text), $translatedItems);

            return $translatedItems;
        } catch (Throwable $e) {
            throw new TranslationException(previous: $e);
        }
    }
}