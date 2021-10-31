<?php

namespace Questions\Repositories;

use Questions\FileHandlers\AbstractFileHandler;
use Questions\Entities\Question;
use Questions\Transformers\AbstractTransformer;

class FileQuestionsRepository implements QuestionsRepositoryInterface
{
    public function __construct(
        protected AbstractTransformer $transformer,
        protected AbstractFileHandler $fileHandler,
        protected string $pathToFile
    ) {
    }

    public function all(): array
    {
        $decodedQuestions = $this->fileHandler->decode($this->pathToFile);
        return array_map(fn(array $question) => $this->transformer->transformFromFile($question), $decodedQuestions);
    }

    public function add(Question $question): Question
    {
        $questions = $this->fileHandler->decode($this->pathToFile);
        $questions[] = $this->transformer->transformToFile($question);
        $text = $this->fileHandler->encode($questions);
        file_put_contents($this->pathToFile, $text, LOCK_EX);
        return $question;
    }
}