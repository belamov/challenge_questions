<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use Questions\Repositories\QuestionsRepositoryInterface;
use Questions\Services\Translation\Translator;

class QuestionsController extends Controller
{
    public function __construct(
        protected QuestionsRepositoryInterface $questionsRepository,
        protected Translator $translator
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        //TODO: validation of language - it must be ISO-639-1 code
        $this->validate($request, [
            'lang' => 'required|string|size:2'
        ]);

        $questions = $this->questionsRepository->all();

        $language = $request->get('lang');

        $translatedQuestions = $this->translator->translateQuestions($questions, $language);

        return JsonResource::collection($translatedQuestions);
    }
}
