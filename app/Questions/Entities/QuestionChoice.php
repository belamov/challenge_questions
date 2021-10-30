<?php

namespace Questions\Entities;

class QuestionChoice
{
    public function __construct(private string $text)
    {
    }

    public function getText(): string
    {
        return $this->text;
    }
}