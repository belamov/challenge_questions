<?php

use Questions\Entities\Question;
use Questions\Entities\QuestionChoicesCollection;
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
            QuestionChoicesCollection::fromArray([
                'Some Choice',
                'Open Assignment Technologies',
                'weird choice',
            ])
        );
        $this->assertInstanceOf(Translatable::class, $question);

        $translatedQuestionText = 'translated question text';
        $translatedChoiceText = 'translated choice text';
        $mockedTranslationEngine = $this->getMockedTranslationEngine($translatedChoiceText, $translatedQuestionText);

        $translatedQuestion = $question->translate($mockedTranslationEngine, 'ru');

        $this->assertInstanceOf(Question::class, $translatedQuestion);
        $this->assertEquals($translatedQuestionText, $translatedQuestion->getText());
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
            QuestionChoicesCollection::fromArray([
                'Some Choice',
                'Open Assignment Technologies',
                'weird choice',
            ])
        );
        $question2 = new Question(
            'some text',
            new DateTime(),
            QuestionChoicesCollection::fromArray([
                'Some Choice',
                'Open Assignment Technologies',
                'weird choice',
            ])
        );

        $translatedQuestionText = 'translated text';
        $translatedChoiceText = 'translated text';
        $mockedTranslationEngine = $this->getMockedTranslationEngine($translatedChoiceText, $translatedQuestionText);

        $translator = new Translator($mockedTranslationEngine);
        $translatedQuestions = $translator->translateItems([$question1, $question2], 'ru');
        foreach ($translatedQuestions as $translatedQuestion) {
            $this->assertInstanceOf(Question::class, $translatedQuestion);
            $this->assertEquals($translatedQuestionText, $translatedQuestion->getText());
            foreach ($translatedQuestion->getChoices() as $translatedChoice) {
                $this->assertEquals($translatedChoiceText, $translatedChoice->getText());
            }
        }
    }
}
