<?php

namespace Questions\Transformers;

use DateTime;
use DateTimeInterface;
use JsonException;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Exceptions\ParsingException;
use Throwable;

abstract class AbstractTransformer
{
    public function transformFromFile(array $data): Question
    {
        $createdAt = $this->parseCreatedAt($data);
        $text = $this->parseText($data);
        $choices = $this->parseChoices($data);

        return new Question($text, $createdAt, $choices);
    }

    abstract public function transformToFile(Question $question): array;

    abstract protected function parseCreatedAt(array $data): DateTimeInterface;

    abstract protected function parseText(array $data): string;

    /**
     * @return array<QuestionChoice>
     */
    abstract protected function parseChoices(array $data): array;
}