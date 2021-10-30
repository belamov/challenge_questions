<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoice;
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

    /**
     * @throws ValidationException
     */
    public function create(Request $request): Response
    {
        $this->validate($request, [
            'text' => 'required|string',
            'createdAt' => 'required|date',
            'choices' => 'required|array|size:3',
            'choices.*.text' => 'required|string'
        ]);

        $newQuestion = new Question(
            text: $request->get('text'),
            createdAt: new DateTime($request->get('createdAt')),
            choices: array_map(
                static fn(array $choice) => new QuestionChoice($choice['text']),
                $request->get('choices')
            )
        );

        $addedQuestion = $this->questionsRepository->add($newQuestion);

        return new Response(new JsonResource($addedQuestion), Response::HTTP_CREATED);
    }
}
