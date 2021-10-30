<?php

namespace Questions\Repositories;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Questions\Transformers\CsvTransformer;

class CsvQuestionsRepository implements QuestionsRepositoryInterface
{
    protected string $pathToCsv;
    protected CsvTransformer $transformer;

    /**
     * @throws FileNotFoundException
     */
    public function __construct(CsvTransformer $transformer, string $pathToCsv)
    {
        if (!file_exists($pathToCsv)) {
            throw new FileNotFoundException("file '$pathToCsv' not found");
        }

        $this->pathToCsv = $pathToCsv;
        $this->transformer = $transformer;
    }

    /**
     * @throws Exception
     */
    public function all(): array
    {
        $decodedQuestions = $this->decodeCsv($this->pathToCsv);
        return array_map(fn(array $question) => $this->transformer->transform($question), $decodedQuestions);
    }

    private function decodeCsv(string $pathToCsv): array
    {
        //for now lets assume that csv files suppose to be small,
        //so we will manage them in memory with no problems
        // TODO: add decoding of large csv files
        // TODO: handling headings row?
        $result = [];
        $row = 0;
        if (($handle = fopen($pathToCsv, 'rb')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                if ($row !== 0) {
                    $result[] = $data;
                }
                $row++;
            }
            fclose($handle);
        }
        return $result;
    }
}