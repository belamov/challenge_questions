<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Questions\Decoders\AbstractFileDecoder;
use Questions\Decoders\JsonFileDecoder;
use Questions\Repositories\FileQuestionsRepository;
use Questions\Repositories\QuestionsRepositoryInterface;
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
            ->giveConfig('questions.json_path');
        $this->app->bind(QuestionsRepositoryInterface::class, FileQuestionsRepository::class);
        $this->app->bind(AbstractFileDecoder::class, JsonFileDecoder::class);
        $this->app->bind(AbstractTransformer::class, JsonTransformer::class);
    }

    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
