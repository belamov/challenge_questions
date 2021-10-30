<?php

namespace Questions\Entities;

use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;

class Question implements Arrayable
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

    public function toArray(): array
    {
        return [
            'text' => $this->getText(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
            'choices' => array_map(static fn(QuestionChoice $choice) => $choice->toArray(), $this->getChoices())
        ];
    }
}