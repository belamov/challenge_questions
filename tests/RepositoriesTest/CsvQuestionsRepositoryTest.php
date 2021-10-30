<?php

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Questions\Decoders\CsvFileDecoder;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Exceptions\ParsingException;
use Questions\Repositories\FileQuestionsRepository;
use Questions\Transformers\CsvTransformer;

class CsvQuestionsRepositoryTest extends TestCase
{
    /** @test */
    public function it_throws_file_not_found_exception_if_csv_doesnt_exists(): void
    {
        $this->expectException(FileNotFoundException::class);
        $repository = new FileQuestionsRepository(
            new CsvTransformer(),
            new CsvFileDecoder(),
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
            new CsvFileDecoder(),
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
            new CsvFileDecoder(),
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
            new CsvFileDecoder(),
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
}
