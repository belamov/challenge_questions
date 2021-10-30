<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Questions\Repositories\QuestionsRepositoryInterface;

class QuestionsController extends Controller
{
    protected QuestionsRepositoryInterface $questionsRepository;

    public function __construct(QuestionsRepositoryInterface $questions)
    {
        $this->questionsRepository = $questions;
    }

    public function index(): AnonymousResourceCollection
    {
        $questions = $this->questionsRepository->all();

        return JsonResource::collection($questions);
    }
}
