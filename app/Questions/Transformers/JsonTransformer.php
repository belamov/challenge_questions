<?php

namespace Questions\Transformers;

use DateTime;
use DateTimeInterface;
use JsonException;
use Questions\Entities\QuestionChoice;
use Questions\Exceptions\ParsingException;
use Throwable;

class JsonTransformer extends AbstractTransformer
{
    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseCreatedAt(array $data): DateTimeInterface
    {
        try {
            return new DateTime($data['createdAt']);
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
    protected function parseText(array $data): string
    {
        try {
            return $data['text'];
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
    protected function parseChoices(array $data): array
    {
        try {
            $choices = $data['choices'];
            return array_map(static fn($choice) => new QuestionChoice($choice['text']), $choices);
        } catch (Throwable $exception) {
            throw new ParsingException(
                message: "unable to parse json: ".json_encode($data, JSON_THROW_ON_ERROR),
                previous: $exception
            );
        }
    }
}