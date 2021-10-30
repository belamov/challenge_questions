<?php

namespace Questions\Entities;

use Illuminate\Contracts\Support\Arrayable;

class QuestionChoice implements Arrayable
{
    public function __construct(private string $text)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function toArray(): array
    {
        return [
            'text' => $this->getText()
        ];
    }
}