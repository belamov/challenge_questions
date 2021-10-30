<?php

namespace Questions\Repositories;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Questions\Decoders\AbstractFileDecoder;
use Questions\Exceptions\ParsingException;
use Questions\Transformers\AbstractTransformer;
use Questions\Transformers\CsvTransformer;

class FileQuestionsRepository implements QuestionsRepositoryInterface
{
    protected string $pathToFile;
    protected AbstractTransformer $transformer;
    protected AbstractFileDecoder $decoder;

    public function __construct(AbstractTransformer $transformer, AbstractFileDecoder $decoder, string $pathToFile)
    {
        $this->pathToFile = $pathToFile;
        $this->transformer = $transformer;
        $this->decoder = $decoder;
    }

    /**
     * @throws FileNotFoundException
     */
    public function all(): array
    {
        $this->decoder->checkFileExists($this->pathToFile);
        $decodedQuestions = $this->decoder->decode($this->pathToFile);
        return array_map(fn(array $question) => $this->transformer->transform($question), $decodedQuestions);
    }
}