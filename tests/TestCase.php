<?php

use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Mockery\MockInterface;
use Questions\Services\Translation\Engines\TranslatorEngineInterface;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
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
                $mock->shouldReceive('translate')->withArgs(function ($argument) {
                    return count($argument) === 4;
                })->andReturn([
                    $translatedQuestionText,
                    $translatedChoiceText,
                    $translatedChoiceText,
                    $translatedChoiceText,
                ]);
            }
        );
    }
}
