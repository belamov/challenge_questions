<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Repositories\QuestionsRepositoryInterface;

class IndexQuestionsTest extends TestCase
{
    /** @test */
    public function it_fetches_questions(): void
    {
        $question1 = new Question(
            'text1',
            new DateTime('2019-06-01 00:00:00'),
            [
                new QuestionChoice('choice1'),
                new QuestionChoice('choice2'),
                new QuestionChoice('choice3'),
            ]
        );
        $question2 = new Question(
            'text2',
            new DateTime('2019-06-01 00:00:00'),
            [
                new QuestionChoice('choice1'),
                new QuestionChoice('choice2'),
                new QuestionChoice('choice3'),
            ]
        );
        $questions = [
            $question1,
            $question2
        ];
        $this->app->instance(
            QuestionsRepositoryInterface::class,
            Mockery::mock(QuestionsRepositoryInterface::class, function (MockInterface $mock) use ($questions) {
                $mock->shouldReceive('all')->once()->andReturn($questions);
            })
        );

        $this->get(route('questions.index'));
        $this->response->assertOk();
        $this->seeJsonEquals([$question1->toArray(), $question2->toArray()]);
        $this->seeJsonStructure([
            '*' => [
                'text',
                'createdAt',
                'choices' => [
                    '*' => [
                        'text'
                    ]
                ]
            ]
        ]);
    }
}
