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
    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseCreatedAt(array $data): DateTimeInterface
    {
        try {
            return new DateTime($data[1]);
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
    protected function parseText(array $data): string
    {
        try {
            return $data[0];
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
    protected function parseChoices(array $data): array
    {
        try {
            return [
                new QuestionChoice($data[2]),
                new QuestionChoice($data[3]),
                new QuestionChoice($data[4]),
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
        $data = [
            $question->getText(),
            $question->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
        foreach ($question->getChoices() as $choice) {
            $data[] = $choice->getText();
        }
        return $data;
    }
}