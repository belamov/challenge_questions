<?php

use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
use Questions\Services\Translation\Translatable;
use Questions\Services\Translation\Translator;

class TranslatorTest extends TestCase
{
    /** @test */
    public function question_can_translate_itself(): void
    {
        $question = new Question(
            'some text',
            new DateTime(),
            [
                new QuestionChoice('Some Choice'),
                new QuestionChoice('Open Assignment Technologies'),
                new QuestionChoice('weird choice'),
            ]
        );
        $this->assertInstanceOf(Translatable::class, $question);

        $translatedQuestionText = 'translated text';
        $translatedChoiceText = 'translated text';
        $mockedTranslationEngine = $this->getMockedTranslationEngine($translatedChoiceText, $translatedQuestionText);

        $translatedQuestion = $question->translate($mockedTranslationEngine, 'ru');

        $this->assertInstanceOf(Question::class, $translatedQuestion);
        $this->assertEquals($translatedChoiceText, $translatedQuestion->getText());
        foreach ($translatedQuestion->getChoices() as $translatedChoice) {
            $this->assertEquals($translatedChoiceText, $translatedChoice->getText());
        }
    }

    /** @test */
    public function it_translates_questions(): void
    {
        $question1 = new Question(
            'some text',
            new DateTime(),
            [
                new QuestionChoice('Some Choice'),
                new QuestionChoice('Open Assignment Technologies'),
                new QuestionChoice('weird choice'),
            ]
        );
        $question2 = new Question(
            'some text',
            new DateTime(),
            [
                new QuestionChoice('Open '),
                new QuestionChoice('Some'),
                new QuestionChoice('weird choice'),
            ]
        );

        $translatedQuestionText = 'translated text';
        $translatedChoiceText = 'translated text';
        $mockedTranslationEngine = $this->getMockedTranslationEngine($translatedChoiceText, $translatedQuestionText);

        $translator = new Translator($mockedTranslationEngine);
        $translatedQuestions = $translator->translateQuestions([$question1, $question2], 'ru');
        foreach ($translatedQuestions as $translatedQuestion) {
            $this->assertInstanceOf(Question::class, $translatedQuestion);
            $this->assertEquals($translatedQuestionText, $translatedQuestion->getText());
            foreach ($translatedQuestion->getChoices() as $translatedChoice) {
                $this->assertEquals($translatedChoiceText, $translatedChoice->getText());
            }
        }
    }
}
