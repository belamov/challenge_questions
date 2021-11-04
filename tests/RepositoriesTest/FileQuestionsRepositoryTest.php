<?php

use Questions\Entities\Question;
use Questions\Entities\QuestionChoicesCollection;
use Questions\Exceptions\FileWritingException;
use Questions\FileHandlers\CsvFileHandler;
use Questions\Repositories\FileQuestionsRepository;
use Questions\Transformers\CsvTransformer;

class FileQuestionsRepositoryTest extends TestCase
{
    /** @test */
    public function ie_throws_exception_when_file_lock_cannot_be_acquired(): void
    {
        $this->expectException(FileWritingException::class);
        $filePath = __DIR__.'/csvs/questions.csv ';
        $f = fopen($filePath, 'cb');

        $repository = new FileQuestionsRepository(
            new CsvTransformer(),
            new CsvFileHandler(),
            $filePath
        );

        $newQuestion = new Question(
            'text',
            new DateTime(),
            QuestionChoicesCollection::fromArray([
                'choice1',
                'choice2',
                'choice2'
            ])
        );
        $repository->add($newQuestion);

        flock($f, LOCK_EX | LOCK_NB);

        $this->assertNotEmpty($repository->all());
        $this->assertCount(1, $repository->all());

        try {
            $repository->add($newQuestion);
        } catch (Throwable $exception) {
            $this->assertCount(1, $repository->all());
            flock($f, LOCK_UN);
            unlink($filePath);
            throw $exception;
        }
    }
}
