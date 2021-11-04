<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Questions\Exceptions\QuestionsException;
use Questions\FileHandlers\AbstractFileHandler;
use Questions\FileHandlers\CsvFileHandler;
use Questions\FileHandlers\JsonFileHandler;
use Questions\Repositories\FileQuestionsRepository;
use Questions\Repositories\QuestionsRepositoryInterface;
use Questions\Services\Translation\Engines\GoogleTranslatorEngine;
use Questions\Services\Translation\Engines\TranslatorEngineInterface;
use Questions\Transformers\AbstractTransformer;
use Questions\Transformers\CsvTransformer;
use Questions\Transformers\JsonTransformer;

class QuestionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $fileFormat = $this->app->get('config')->get('questions.file_format');

        $fileHandler = $this->getFileHandler($fileFormat);
        $transformer = $this->getTransformer($fileFormat);
        $repositoryImplementation = $this->getRepository();
        $translator = $this->getTranslatorService();

        $this->app->when(FileQuestionsRepository::class)
            ->needs('$pathToFile')
            ->give(function ($container) {
                return $container->get('config')->get('questions.file_path');
            });

        $this->app->bind(QuestionsRepositoryInterface::class, $repositoryImplementation);
        $this->app->bind(AbstractFileHandler::class, $fileHandler);
        $this->app->bind(AbstractTransformer::class, $transformer);
        $this->app->bind(TranslatorEngineInterface::class, $translator);
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }

    protected function getRepository(): string
    {
        return FileQuestionsRepository::class;
    }

    protected function getTranslatorService(): string
    {
        return GoogleTranslatorEngine::class;
    }

    /**
     * @throws QuestionsException
     */
    private function getFileHandler(string $fileFormat): string
    {
        return match ($fileFormat) {
            'csv' => CsvFileHandler::class,
            'json' => JsonFileHandler::class,
            default => throw new QuestionsException(
                "cant resolve file handler dependency. unsupported file format '$fileFormat'"
            ),
        };
    }

    /**
     * @throws QuestionsException
     */
    private function getTransformer(string $fileFormat): string
    {
        return match ($fileFormat) {
            'csv' => CsvTransformer::class,
            'json' => JsonTransformer::class,
            default => throw new QuestionsException(
                "cant resolve transformer dependency. unsupported file format '$fileFormat'"
            ),
        };
    }
}
