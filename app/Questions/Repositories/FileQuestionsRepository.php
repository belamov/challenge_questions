<?php

namespace Questions\Repositories;

use Questions\Decoders\AbstractFileDecoder;
use Questions\Entities\Question;
use Questions\Transformers\AbstractTransformer;

class FileQuestionsRepository implements QuestionsRepositoryInterface
{
    public function __construct(
        protected AbstractTransformer $transformer,
        protected AbstractFileDecoder $decoder,
        protected string $pathToFile
    ) {
    }

    public function all(): array
    {
        $decodedQuestions = $this->decoder->decode($this->pathToFile);
        return array_map(fn(array $question) => $this->transformer->transformFromFile($question), $decodedQuestions);
    }

    public function add(Question $question): Question
    {
        $questions = $this->decoder->decode($this->pathToFile);
        $questions[] = $this->transformer->transformToFile($question);
        $text = $this->decoder->encode($questions);
        file_put_contents($this->pathToFile, $text, LOCK_EX);
        return $question;
    }
}