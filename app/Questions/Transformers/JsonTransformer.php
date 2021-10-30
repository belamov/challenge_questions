<?php

namespace Questions\Transformers;

use DateTime;
use DateTimeInterface;
use Exception;
use JsonException;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Exceptions\ParsingException;
use Throwable;

class JsonTransformer
{
    /**
     * @throws ParsingException
     * @throws JsonException
     */
    public function transform(array $data): Question
    {
        $createdAt = $this->parseCreatedAt($data);
        $text = $this->parseText($data);
        $choices = $this->parseChoices($data);

        return new Question($text, $createdAt, $choices);
    }

    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseCreatedAt($data): DateTimeInterface
    {
        try {
            return new DateTime($data['createdAt']);
        } catch (Throwable $exception) {
            throw new ParsingException("unable to parse json: ".json_encode($data, JSON_THROW_ON_ERROR), previous: $exception);
        }
    }

    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseText($data): string
    {
        try {
            return $data['text'];
        } catch (Throwable $exception) {
            throw new ParsingException("unable to parse json: ".json_encode($data, JSON_THROW_ON_ERROR), previous: $exception);
        }
    }

    /**
     * @throws ParsingException
     * @throws JsonException
     */
    protected function parseChoices($data): array
    {
        try {
            $choices = $data['choices'];
            return array_map(static fn($choice) => new QuestionChoice($choice['text']), $choices);
        } catch (Throwable $exception) {
            throw new ParsingException("unable to parse json: ".json_encode($data, JSON_THROW_ON_ERROR), previous: $exception);
        }
    }
}