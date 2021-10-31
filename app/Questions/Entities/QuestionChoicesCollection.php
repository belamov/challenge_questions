<?php

namespace Questions\Entities;

use ArrayAccess;
use Countable;
use Questions\Exceptions\QuestionsException;

/**
 * @implements  ArrayAccess<int, QuestionChoice>
 */
class QuestionChoicesCollection implements ArrayAccess, Countable
{
    public const AVAILABLE_CHOICES_COUNT = 3;

    /**
     * @var array<QuestionChoice>
     */
    protected array $choices;


    public function offsetExists($offset): bool
    {
        return isset($this->choices[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->choices[$offset];
    }

    /**
     * @throws QuestionsException
     */
    public function offsetSet($offset, $value): void
    {
        throw new QuestionsException('cant modify question choices. you should create new collection instead');
    }

    /**
     * @throws QuestionsException
     */
    public function offsetUnset($offset): void
    {
        throw new QuestionsException('questions cannot contain less than '.self::AVAILABLE_CHOICES_COUNT.' choices');
    }

    public function count(): int
    {
        return self::AVAILABLE_CHOICES_COUNT;
    }

    /**
     * @throws QuestionsException
     */
    public function __construct(array $choices)
    {
        if (count($choices) !== self::AVAILABLE_CHOICES_COUNT) {
            throw new QuestionsException(sprintf(
                    "there must be %d choices in question",
                    self::AVAILABLE_CHOICES_COUNT
                )
            );
        }

        foreach ($choices as $choice) {
            if (!$choice instanceof QuestionChoice) {
                throw new QuestionsException(sprintf(
                        "choice must be instance of %s",
                        QuestionChoice::class
                    )
                );
            }
        }

        $this->choices = $choices;
    }

    /**
     * @throws QuestionsException
     */
    public static function fromArray(array $choices): QuestionChoicesCollection
    {
        return new self(
            array_map(
                static fn(string $choice) => new QuestionChoice($choice), $choices)
        );
    }

    public function toArray(): array
    {
        return array_map(static fn(QuestionChoice $choice) => $choice->toArray(), $this->choices);
    }

    public function getTexts(): array
    {
        return array_map(static fn(QuestionChoice $choice) => $choice->getText(), $this->choices);
    }
}