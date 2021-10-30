<?php

namespace Questions\Entities;

use DateTimeInterface;

class Question
{
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
}