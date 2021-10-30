<?php

use Mockery\MockInterface;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Repositories\QuestionsRepositoryInterface;
use Questions\Services\Translation\Engines\TranslatorEngineInterface;

class IndexQuestionsTest extends TestCase
{
    /** @test */
    public function it_fetches_translated_questions(): void
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
        $this->app->instance(
            TranslatorEngineInterface::class,
            $this->getMockedTranslationEngine()
        );

        $this->get(route('questions.index', ['lang' => 'ru']));
        $this->response->assertOk();
        $translatedQuestion1 = $question1
            ->translate(
                $this->getMockedTranslationEngine(),
                'ru'
            );
        $translatedQuestion2 = $question2
            ->translate(
                $this->getMockedTranslationEngine(),
                'ru'
            );
        $this->seeJsonEquals([
            $translatedQuestion1->toArray(),
            $translatedQuestion2->toArray(),
        ]);
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

    /** @test */
    public function it_requires_lang_parameter(): void
    {
        $this->get(route('questions.index'));
        $this->response->assertUnprocessable();
        $this->response->assertJsonValidationErrors('lang', null);
    }

    /** @test */
    public function lang_parameter_should_be_2_characters_long_string(): void
    {
        $this->get(route('questions.index', ['lang' => 'russian']));
        $this->response->assertUnprocessable();
        $this->response->assertJsonValidationErrors('lang', null);
    }
}
