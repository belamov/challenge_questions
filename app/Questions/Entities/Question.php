<?php

namespace Questions\Entities;

use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Questions\Services\Translation\Engines\TranslatorEngineInterface;
use Questions\Services\Translation\Translatable;

class Question implements Arrayable, Translatable
{
    /**
     * @param  string  $text
     * @param  DateTimeInterface  $createdAt
     * @param  array<QuestionChoice>  $choices
     */
    public function __construct(
        private string $text,
        private DateTimeInterface $createdAt,
        private array $choices,
    ) {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @return array<QuestionChoice>
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->getText(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'choices' => array_map(static fn(QuestionChoice $choice) => $choice->toArray(), $this->getChoices())
        ];
    }

    public function translate(TranslatorEngineInterface $translatorEngine, string $language): Question
    {
        $itemsToTranslate = [
            $this->text
        ];
        foreach ($this->choices as $choice) {
            $itemsToTranslate[] = $choice->getText();
        }

        $translatedItems = $translatorEngine->translate($itemsToTranslate, $language);

        $translatedQuestionText = array_shift($translatedItems);

        return new self(
            $translatedQuestionText,
            $this->createdAt,
            array_map(static fn($translatedChoiceText) => new QuestionChoice($translatedChoiceText), $translatedItems)
        );
    }
}