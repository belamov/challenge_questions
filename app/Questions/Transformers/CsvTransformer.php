<?php

namespace Questions\Transformers;

use DateTime;
use DateTimeInterface;
use JsonException;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Exceptions\ParsingException;
use Throwable;

class CsvTransformer extends AbstractTransformer
{
    private const QUESTION_TEXT_INDEX = 0;
    private const QUESTION_CREATED_AT_INDEX = 1;
    private const CHOICE_1_TEXT_INDEX = 2;
    private const CHOICE_2_TEXT_INDEX = 3;
    private const CHOICE_3_TEXT_INDEX = 4;

    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseText(array $data): string
    {
        try {
            return $data[self::QUESTION_TEXT_INDEX];
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "unable to parse 'text' from csv: ".json_encode($data, JSON_THROW_ON_ERROR),
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
            return new DateTime($data[self::QUESTION_CREATED_AT_INDEX]);
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "unable to parse 'created at' from csv: ".json_encode($data, JSON_THROW_ON_ERROR),
                previous: $exception
            );
        }
    }

    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseChoices(array $data): array
    {
        try {
            return [
                new QuestionChoice($data[self::CHOICE_1_TEXT_INDEX]),
                new QuestionChoice($data[self::CHOICE_2_TEXT_INDEX]),
                new QuestionChoice($data[self::CHOICE_3_TEXT_INDEX]),
            ];
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "unable to parse choices from csv: ".json_encode($data, JSON_THROW_ON_ERROR),
                previous: $exception
            );
        }
    }

    public function transformToFile(Question $question): array
    {
        $data = [];

        $data[self::QUESTION_TEXT_INDEX] = $question->getText();
        $data[self::QUESTION_CREATED_AT_INDEX] = $question->getCreatedAt()->format('Y-m-d H:i:s');
        $data[self::CHOICE_1_TEXT_INDEX] = $question->getChoices()[0]->getText();
        $data[self::CHOICE_2_TEXT_INDEX] = $question->getChoices()[1]->getText();
        $data[self::CHOICE_3_TEXT_INDEX] = $question->getChoices()[2]->getText();

        return $data;
    }
}