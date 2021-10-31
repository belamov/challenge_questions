<?php


use Mockery\MockInterface;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Repositories\QuestionsRepositoryInterface;

class CreateQuestionsTest extends TestCase
{
    /** @test */
    public function it_adds_question(): void
    {
        $question = new Question(
            'text1',
            new DateTime('2019-06-01 00:00:00'),
            [
                new QuestionChoice('choice1'),
                new QuestionChoice('choice2'),
                new QuestionChoice('choice3'),
            ]
        );
        $this->app->instance(
            QuestionsRepositoryInterface::class,
            Mockery::mock(QuestionsRepositoryInterface::class, function (MockInterface $mock) use ($question) {
                $mock->shouldReceive('add')->once()->andReturn($question);
            })
        );

        $this->post(route('questions.create', $question->toArray()));
        $this->response->assertCreated();
        $this->seeJson($question->toArray());
    }

    /**
     * @test
     * @dataProvider invalidRequestsDataProvider
     */
    public function it_validates_data(array $data, string $errorKey): void
    {
        $this->post(route('questions.create', $data));
        $this->response->assertUnprocessable();
        $this->response->assertJsonValidationErrors($errorKey, null);
    }

    public function invalidRequestsDataProvider(): array
    {
        return [
            [
                [
                    'createdAt' => '2019-06-01 00:00:00',
                    'choices' => [
                        ['text' => 'choice'],
                        ['text' => 'choice'],
                        ['text' => 'choice'],
                    ]
                ],
                'text'
            ],
            [
                [
                    'text' => 'text',
                    'createdAt' => 'invalid date',
                    'choices' => [
                        ['text' => 'choice'],
                        ['text' => 'choice'],
                        ['text' => 'choice'],
                    ]
                ],
                'createdAt'
            ],
            [
                [
                    'text' => 'text',
                    'choices' => [
                        ['text' => 'choice'],
                        ['text' => 'choice'],
                        ['text' => 'choice'],
                    ]
                ],
                'createdAt'
            ],
            [
                [
                    'text' => 'text',
                    'createdAt' => '2019-06-01 00:00:00',
                ],
                'choices'
            ],
            [
                [
                    'text' => 'text',
                    'createdAt' => '2019-06-01 00:00:00',
                    'choices' => [
                        ['choice'],
                        ['text' => 'choice'],
                        ['text' => 'choice'],
                    ]

                ],
                'choices.0.text'
            ],
            [
                [
                    'text' => 'text',
                    'createdAt' => '2019-06-01 00:00:00',
                    'choices' => [
                        ['text' => 'choice'],
                        ['text' => 'choice'],
                    ]

                ],
                'choices'
            ],
            [
                [
                    'text' => 'text',
                    'createdAt' => '2019-06-01 00:00:00',
                    'choices' => "choice,choice,choice"

                ],
                'choices'
            ],
        ];
    }
}
