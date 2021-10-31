<?php

use Questions\Entities\QuestionChoice;
use Questions\Entities\QuestionChoicesCollection;
use Questions\Exceptions\QuestionsException;

class QuestionChoicesCollectionTest extends TestCase
{
    protected function getCollectionWithChoices(?QuestionChoice $choice = null): QuestionChoicesCollection
    {
        foreach (range(0, QuestionChoicesCollection::AVAILABLE_CHOICES_COUNT - 1) as $_) {
            $choices[] = $choice ?? new QuestionChoice('choice');
        }
        return new QuestionChoicesCollection($choices);
    }

    /** @test */
    public function it_throws_exception_when_we_provide_unexpected_number_of_choices(): void
    {
        $this->expectException(QuestionsException::class);
        $choices = [];
        foreach (range(0, QuestionChoicesCollection::AVAILABLE_CHOICES_COUNT + 2) as $_) {
            $choices[] = new QuestionChoice('choice');
        }
        new QuestionChoicesCollection($choices);
    }

    /** @test */
    public function it_throws_exception_when_we_dont_provide_question_choices_as_items(): void
    {
        $this->expectException(QuestionsException::class);
        $choices = [];
        foreach (range(0, QuestionChoicesCollection::AVAILABLE_CHOICES_COUNT - 1) as $_) {
            $choices[] = 'choice';
        }
        new QuestionChoicesCollection($choices);
    }

    /** @test */
    public function we_can_fetch_single_choice_from_collection(): void
    {
        $choice = new QuestionChoice('choice');
        $collection = $this->getCollectionWithChoices($choice);
        $this->assertEquals($choice, $collection[0]);
        $this->assertEquals($choice, $collection[1]);
        $this->assertEquals($choice, $collection[2]);
    }


    /** @test */
    public function it_implements_offset_exists_correctly(): void
    {
        $collection = $this->getCollectionWithChoices();
        $this->assertTrue(isset($collection[0]));
        $this->assertTrue(isset($collection[1]));
        $this->assertTrue(isset($collection[2]));
        $this->assertFalse(isset($collection[3]));
        $this->assertFalse(isset($collection[5]));
        $this->assertFalse(isset($collection[-1]));
    }

    /** @test */
    public function collection_is_immutable_for_modifying(): void
    {
        $this->expectException(QuestionsException::class);
        $collection = $this->getCollectionWithChoices();
        $collection[0] = new QuestionChoice('new choice');
    }

    /** @test */
    public function collection_is_immutable_for_deleting(): void
    {
        $this->expectException(QuestionsException::class);
        $collection = $this->getCollectionWithChoices();
        unset($collection[0]);
    }
}
