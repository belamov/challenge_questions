<?php

namespace Questions\Transformers;

use DateTimeInterface;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoicesCollection;

abstract class AbstractTransformer
{
    public function transformFromFile(array $data): Question
    {
        $createdAt = $this->parseCreatedAt($data);
        $text = $this->parseText($data);
        $choices = $this->parseChoices($data);

        return new Question($text, $createdAt, $choices);
    }

    abstract protected function parseText(array $data): string;

    abstract protected function parseCreatedAt(array $data): DateTimeInterface;

    abstract protected function parseChoices(array $data): QuestionChoicesCollection;

    abstract public function transformToFile(Question $question): array;
}