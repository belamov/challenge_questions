<?php

namespace Questions\Repositories;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use JsonException;
use Questions\Transformers\JsonTransformer;

class JsonQuestionsRepository implements QuestionsRepositoryInterface
{
    protected string $pathToJson;
    protected JsonTransformer $transformer;

    /**
     * @throws FileNotFoundException
     * @throws JsonException
     */
    public function __construct(JsonTransformer $transformer, string $pathToJson)
    {
        if (!file_exists($pathToJson)) {
            throw new FileNotFoundException("file '$pathToJson' not found");
        }

        $this->pathToJson = $pathToJson;
        $this->transformer = $transformer;
    }

    /**
     * @throws JsonException
     * @throws Exception
     */
    public function all(): array
    {
        //for now lets assume that json files suppose to be small,
        //so we will manage them in memory with no problems
        // TODO: add decoding of large json files
        $decodedQuestions = $this->decodeJson($this->pathToJson);
        return array_map(fn(array $question) => $this->transformer->transform($question), $decodedQuestions);
    }

    /**
     * @throws JsonException
     */
    private function decodeJson(string $pathToJson): array
    {
        return json_decode(file_get_contents($pathToJson), true, 512, JSON_THROW_ON_ERROR);
    }
}