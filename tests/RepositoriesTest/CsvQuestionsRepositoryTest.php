<?php

use Questions\FileHandlers\CsvFileHandler;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Exceptions\DecodingException;
use Questions\Exceptions\ParsingException;
use Questions\Repositories\FileQuestionsRepository;
use Questions\Transformers\CsvTransformer;

class CsvQuestionsRepositoryTest extends TestCase
{
    /** @test */
    public function it_throws_file_not_found_exception_if_csv_doesnt_exists(): void
    {
        $this->expectException(DecodingException::class);
        $repository = new FileQuestionsRepository(
            new CsvTransformer(),
            new CsvFileHandler(),
            'invalid path to csv'
        );
        $repository->all();
    }

    /** @test */
    public function it_throws_json_exception_when_invalid_json_provided(): void
    {
        $this->expectException(ParsingException::class);
        $repository = new FileQuestionsRepository(
            new CsvTransformer(),
            new CsvFileHandler(),
            __DIR__.'/csvs/invalid_questions.csv'
        );
        $repository->all();
    }

    /**
     * @test
     * @dataProvider unexpectedJsonsProvider
     */
    public function it_throws_parsing_exception_when_unexpected_json_structure_provided($jsonPath): void
    {
        $this->expectException(ParsingException::class);
        $repository = new FileQuestionsRepository(
            new CsvTransformer(),
            new CsvFileHandler(),
            $jsonPath
        );
        $repository->all();
    }

    public function unexpectedJsonsProvider(): array
    {
        return [
            [__DIR__.'/csvs/unexpected_text_questions.csv'],
            [__DIR__.'/csvs/unexpected_choices_questions.csv'],
            [__DIR__.'/csvs/unexpected_created_at_questions.csv'],
        ];
    }

    /** @test */
    public function it_fetches_repositories_from_json_file(): void
    {
        $repository = new FileQuestionsRepository(
            new CsvTransformer(),
            new CsvFileHandler(),
            __DIR__.'/csvs/questions.csv'
        );

        $questions = $repository->all();

        $this->assertInstanceOf(Question::class, $questions[0]);
        $this->assertEquals("What is the capital of Luxembourg ?", $questions[0]->getText());
        $this->assertEquals(new DateTime("2019-06-01 00:00:00"), $questions[0]->getCreatedAt());
        $this->assertIsArray($questions[0]->getChoices());
        $choices = $questions[0]->getChoices();
        $this->assertCount(3, $choices);
        foreach ($choices as $choice) {
            $this->assertInstanceOf(QuestionChoice::class, $choice);
        }
        $this->assertEquals("Luxembourg", $choices[0]->getText());
        $this->assertEquals("Paris", $choices[1]->getText());
        $this->assertEquals("Berlin", $choices[2]->getText());

        $this->assertInstanceOf(Question::class, $questions[1]);
        $this->assertEquals("What does mean O.A.T. ?", $questions[1]->getText());
        $this->assertEquals(new DateTime("2019-06-02 00:00:00"), $questions[1]->getCreatedAt());
        $this->assertIsArray($questions[1]->getChoices());
        $choices = $questions[1]->getChoices();
        $this->assertCount(3, $choices);
        foreach ($choices as $choice) {
            $this->assertInstanceOf(QuestionChoice::class, $choice);
        }
        $this->assertEquals("Open Assignment Technologies", $choices[0]->getText());
        $this->assertEquals("Open Assessment Technologies", $choices[1]->getText());
        $this->assertEquals("Open Acknowledgment Technologies", $choices[2]->getText());
    }

    /** @test */
    public function it_adds_question_to_csv_file(): void
    {
        $filePath = __DIR__.'/csvs/questions-add.csv';
        copy(__DIR__.'/csvs/questions.csv', $filePath);
        $repository = new FileQuestionsRepository(
            new CsvTransformer(),
            new CsvFileHandler(),
            $filePath
        );
        $this->assertCount(2, $repository->all());

        $newQuestion = new Question(
            'text',
            new DateTime(),
            [
                new QuestionChoice('choice1'),
                new QuestionChoice('choice2'),
                new QuestionChoice('choice3'),
            ]
        );

        $addedQuestion = $repository->add($newQuestion);
        $this->assertEquals($newQuestion, $addedQuestion);
        $this->assertCount(3, $repository->all());
        $this->assertEquals($newQuestion->toArray(), $repository->all()[2]->toArray());
        unlink($filePath);
    }

    /** @test */
    public function it_writes_heading_row_if_file_is_empty(): void
    {
        $filePath = __DIR__.'/csvs/questions-add-empty.csv';
        $f = fopen($filePath, 'wb');
        fclose($f);

        $newQuestion = new Question(
            'text',
            new DateTime(),
            [
                new QuestionChoice('choice1'),
                new QuestionChoice('choice2'),
                new QuestionChoice('choice3'),
            ]
        );

        $fileHandler = new CsvFileHandler();
        $transformer = new CsvTransformer();

        $fileContents = $fileHandler->encode([$transformer->transformToFile($newQuestion)]);
        $rows = explode(PHP_EOL, $fileContents);
        // one empty line in the end
        $this->assertCount(3, $rows);

        $generatedHeadingRow = explode(',', $rows[0]);
        $this->assertCount(5, $generatedHeadingRow);
        $this->assertEquals('"Question text"', $generatedHeadingRow[0]);
        $this->assertEquals('"Created At"', $generatedHeadingRow[1]);
        $this->assertEquals('"Choice 1"', $generatedHeadingRow[2]);
        $this->assertEquals('"Choice 2"', $generatedHeadingRow[3]);
        $this->assertEquals('"Choice 3"', $generatedHeadingRow[4]);
        unlink($filePath);
    }
}
