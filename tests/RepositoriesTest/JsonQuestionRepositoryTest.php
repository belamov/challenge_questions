<?php

use Questions\Decoders\JsonFileDecoder;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Exceptions\DecodingException;
use Questions\Exceptions\ParsingException;
use Questions\Repositories\FileQuestionsRepository;
use Questions\Transformers\JsonTransformer;

class JsonQuestionRepositoryTest extends TestCase
{
    /** @test */
    public function it_throws_file_not_found_exception_if_json_doesnt_exists(): void
    {
        $this->expectException(DecodingException::class);
        $repository = new FileQuestionsRepository(
            new JsonTransformer(),
            new JsonFileDecoder(),
            'wrong path to json'
        );
        $repository->all();
    }

    /** @test */
    public function it_throws_json_exception_when_invalid_json_provided(): void
    {
        $this->expectException(DecodingException::class);
        $repository = new FileQuestionsRepository(
            new JsonTransformer(),
            new JsonFileDecoder(),
            __DIR__.'/jsons/invalid_questions.json'
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
            new JsonTransformer(),
            new JsonFileDecoder(),
            $jsonPath
        );
        $repository->all();
    }

    public function unexpectedJsonsProvider(): array
    {
        return [
            [__DIR__.'/jsons/unexpected_text_questions.json'],
            [__DIR__.'/jsons/unexpected_choices_questions.json'],
            [__DIR__.'/jsons/unexpected_created_at_questions.json'],
        ];
    }

    /** @test */
    public function it_fetches_repositories_from_json_file(): void
    {
        $repository = new FileQuestionsRepository(
            new JsonTransformer(),
            new JsonFileDecoder(),
            __DIR__.'/jsons/questions.json'
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
        $filePath = __DIR__.'/jsons/questions-add.json';
        copy(__DIR__.'/jsons/questions.json', $filePath);
        $repository = new FileQuestionsRepository(
            new JsonTransformer(),
            new JsonFileDecoder(),
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
}
