<?php

namespace Questions\Entities;

use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Questions\Exceptions\QuestionsException;
use Questions\Exceptions\TranslationException;
use Questions\Services\Translation\Engines\TranslatorEngineInterface;
use Questions\Services\Translation\Translatable;

class Question implements Arrayable, Translatable
{
    /**
     * @param  string  $text
     * @param  DateTimeInterface  $createdAt
     * @param  QuestionChoicesCollection  $choices
     */
    public function __construct(
        private string $text,
        private DateTimeInterface $createdAt,
        private QuestionChoicesCollection $choices,
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

    public function getChoices(): QuestionChoicesCollection
    {
        return $this->choices;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->getText(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'choices' => $this->choices->toArray()
        ];
    }

    /**
     * @throws TranslationException
     */
    public function translate(TranslatorEngineInterface $translatorEngine, string $language): Question
    {
        $itemsToTranslate = array_merge([$this->text], $this->choices->getTexts());

        $translatedItems = $translatorEngine->translate($itemsToTranslate, $language);

        $translatedQuestionText = array_shift($translatedItems);

        try {
            return new self(
                $translatedQuestionText,
                $this->createdAt,
                QuestionChoicesCollection::fromArray($translatedItems)
            );
        } catch (QuestionsException $e) {
            throw new TranslationException(
                message: 'there was error while translating question choices',
                previous: $e
            );
        }
    }
}