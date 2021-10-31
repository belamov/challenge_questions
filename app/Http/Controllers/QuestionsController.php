<?php

namespace App\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;
use Questions\Entities\Question;
use Questions\Entities\QuestionChoicesCollection;
use Questions\Repositories\QuestionsRepositoryInterface;
use Questions\Services\Translation\Translator;

class QuestionsController extends BaseController
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
        $this->validate($request, [
            'lang' => 'required|string|size:2'
        ]);

        $questions = $this->questionsRepository->all();

        $language = $request->get('lang');

        $translatedQuestions = $this->translator->translateItems($questions, $language);

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
            choices: QuestionChoicesCollection::fromArray(
                array_map(
                    static fn(array $choice) => $choice['text'],
                    $request->get('choices')
                )
            )
        );

        $this->questionsRepository->add($newQuestion);

        return new Response(new JsonResource($newQuestion), Response::HTTP_CREATED);
    }
}
