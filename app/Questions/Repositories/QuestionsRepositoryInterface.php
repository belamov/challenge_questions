<?php

namespace Questions\Repositories;

use Questions\Entities\Question;

interface QuestionsRepositoryInterface
{
    /**
     * @return array<Question>
     */
    public function all(): array;

    public function add(Question $question): Question;
}