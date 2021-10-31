<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Questions\FileHandlers\AbstractFileHandler;
use Questions\FileHandlers\JsonFileHandler;
use Questions\Repositories\FileQuestionsRepository;
use Questions\Repositories\QuestionsRepositoryInterface;
use Questions\Services\Translation\Engines\GoogleTranslatorEngine;
use Questions\Services\Translation\Engines\TranslatorEngineInterface;
use Questions\Transformers\AbstractTransformer;
use Questions\Transformers\JsonTransformer;

class QuestionsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->when(FileQuestionsRepository::class)
            ->needs('$pathToFile')
            ->give(function ($container) {
                return $container->get('config')->get('questions.json_path');
            });
        $this->app->bind(QuestionsRepositoryInterface::class, FileQuestionsRepository::class);
        $this->app->bind(AbstractFileHandler::class, JsonFileHandler::class);
        $this->app->bind(AbstractTransformer::class, JsonTransformer::class);

        $this->app->bind(TranslatorEngineInterface::class, GoogleTranslatorEngine::class);
    }

    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
