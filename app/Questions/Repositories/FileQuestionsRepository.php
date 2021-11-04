<?php

namespace Questions\Repositories;

use Questions\Exceptions\DecodingException;
use Questions\Exceptions\EncodingException;
use Questions\Exceptions\FileWritingException;
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

    /**
     * @throws FileWritingException
     * @throws EncodingException
     * @throws DecodingException
     */
    public function add(Question $question): Question
    {
        $fileResource = $this->acquireFileLock();
        $questions = $this->fileHandler->decode($this->pathToFile);
        $questions[] = $this->transformer->transformToFile($question);
        $text = $this->fileHandler->encode($questions);
        $this->writeToFile($fileResource, $text);
        $this->releaseLock($fileResource);
        return $question;
    }

    /**
     * @return resource $fileResource
     * @throws FileWritingException
     */
    private function acquireFileLock()
    {
        $lockWait = 1;       // seconds to wait for lock
        $waitTime = 250000;  // microseconds to wait between lock attempts
        // 1s / 250000us = 4 attempts.
        $waitSum = 0;
        $fp = fopen($this->pathToFile, 'cb');

        if (!$fp) {
            throw new FileWritingException("Couldnt open file '{$this->pathToFile}'");
        }

        $locked = flock($fp, LOCK_EX | LOCK_NB);
        while (!$locked && ($waitSum <= $lockWait)) {
            $waitSum += $waitTime / 1000000; // microseconds to seconds
            usleep($waitTime);
            $locked = flock($fp, LOCK_EX | LOCK_NB);
        }
        if (!$locked) {
            fclose($fp);
            throw new FileWritingException("Could not lock '{$this->pathToFile} for write within $lockWait seconds.");
        }
        return $fp;
    }

    /**
     * @param  resource  $file
     * @param  string  $text
     */
    protected function writeToFile($file, string $text): void
    {
        ftruncate($file, 0);
        fwrite($file, $text);
    }

    /**
     * @param  resource  $file
     */
    private function releaseLock($file): void
    {
        flock($file, LOCK_UN);
        fclose($file);
    }
}