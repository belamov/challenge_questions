<?php

namespace Questions\Transformers;

use DateTime;
use DateTimeInterface;
use JsonException;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoicesCollection;
use Questions\Exceptions\ParsingException;
use Throwable;

class JsonTransformer extends AbstractTransformer
{
    private const QUESTION_TEXT_KEY = 'text';
    private const QUESTION_CREATED_AT_KEY = 'createdAt';
    private const QUESTION_CHOICES_KEY = 'choices';
    private const CHOICE_TEXT_KEY = 'text';

    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseText(array $data): string
    {
        try {
            return $data[self::QUESTION_TEXT_KEY];
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "unable to parse json: ".json_encode($data, JSON_THROW_ON_ERROR),
                previous: $exception
            );
        }
    }

    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseCreatedAt(array $data): DateTimeInterface
    {
        try {
            return new DateTime($data[self::QUESTION_CREATED_AT_KEY]);
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "unable to parse json: ".json_encode($data, JSON_THROW_ON_ERROR),
                previous: $exception
            );
        }
    }

    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseChoices(array $data): QuestionChoicesCollection
    {
        try {
            $choices = $data[self::QUESTION_CHOICES_KEY];
            return QuestionChoicesCollection::fromArray(
                array_map(static fn($choice) => $choice[self::CHOICE_TEXT_KEY], $choices)
            );
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "unable to parse json: ".json_encode($data, JSON_THROW_ON_ERROR),
                previous: $exception
            );
        }
    }

    public function transformToFile(Question $question): array
    {
        $data = [
            self::QUESTION_TEXT_KEY => $question->getText(),
            self::QUESTION_CREATED_AT_KEY => $question->getCreatedAt()->format('Y-m-d H:i:s'),
            self::QUESTION_CHOICES_KEY => [],
        ];
        foreach ($question->getChoices()->getTexts() as $choiceText) {
            $data[self::QUESTION_CHOICES_KEY][] = [self::CHOICE_TEXT_KEY => $choiceText];
        }
        return $data;
    }
}