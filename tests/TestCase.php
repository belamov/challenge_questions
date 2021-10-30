<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Mockery\MockInterface;
use Questions\Services\Translation\Engines\TranslatorEngineInterface;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function getMockedTranslationEngine(
        string $translatedChoiceText = 'choice',
        string $translatedQuestionText = 'question'
    ): mixed {
        return Mockery::mock(
            TranslatorEngineInterface::class,
            function (MockInterface $mock) use ($translatedChoiceText, $translatedQuestionText) {
                $mock->shouldReceive('translate')->andReturn([
                    $translatedQuestionText,
                    $translatedChoiceText,
                    $translatedChoiceText,
                    $translatedChoiceText,
                ]);
            }
        );
    }
}
