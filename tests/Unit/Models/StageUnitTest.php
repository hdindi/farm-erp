<?php

namespace Tests\Unit\Models; // Ensure this namespace matches your directory structure

use Tests\TestCase;
use App\Models\Stage;
use App\Models\DailyRecord;
use App\Models\Batch;        // Dependency for DailyRecord
use App\Models\BirdType;     // Dependency for Batch
use App\Models\Breed;        // Dependency for Batch
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StageUnitTest extends TestCase
{
    use RefreshDatabase; // Resets the database for each test

    /**
     * Test if a Stage can be created with valid attributes.
     *
     * @return void
     */
    public function test_stage_can_be_created(): void
    {
        $stageData = [
            'name' => 'Starter Phase',
            'description' => 'Initial phase for young birds.',
            'min_age_days' => 1,
            'max_age_days' => 28,
            'target_weight_grams' => 500,
        ];

        $stage = Stage::create($stageData);

        $this->assertInstanceOf(Stage::class, $stage);
        $this->assertEquals($stageData['name'], $stage->name);
        $this->assertEquals($stageData['min_age_days'], $stage->min_age_days);
        $this->assertEquals($stageData['max_age_days'], $stage->max_age_days);
        $this->assertDatabaseHas('stages', $stageData);
    }

    /**
     * Test the fillable attributes of the Stage model.
     *
     * @return void
     */
    public function test_stage_has_correct_fillable_attributes(): void
    {
        $stage = new Stage();
        // These should match the $fillable array in your App\Models\Stage model
        $expectedFillable = [
            'name',
            'description',
            'min_age_days',
            'max_age_days',
            'target_weight_grams',
        ];
        $this->assertEquals($expectedFillable, $stage->getFillable());
    }

    /**
     * Test attribute casting for the Stage model.
     *
     * @return void
     */
    public function test_stage_attribute_casting(): void
    {
        // Create a stage using the factory or direct creation
        $stage = Stage::factory()->create([
            'min_age_days' => '7', // Pass as string to test casting
            'max_age_days' => '21',
            'target_weight_grams' => '1200',
        ]);

        // Retrieve the model to ensure casts are applied on retrieval
        $retrievedStage = Stage::find($stage->id);

        $this->assertIsInt($retrievedStage->min_age_days);
        $this->assertIsInt($retrievedStage->max_age_days);
        $this->assertIsInt($retrievedStage->target_weight_grams);
    }


    /**
     * Test the 'dailyRecords' relationship.
     * A Stage can have many DailyRecords.
     *
     * @return void
     */
    public function test_stage_has_many_daily_records_relationship(): void
    {
        // Create a Stage
        $stage = Stage::factory()->create();

        // Create associated dependencies for DailyRecord
        $birdType = BirdType::factory()->create();
        $breed = Breed::factory()->create();
        $batch = Batch::factory()->create([
            'bird_type_id' => $birdType->id,
            'breed_id' => $breed->id,
        ]);

        // Create DailyRecords associated with this Stage and Batch
        $dailyRecord1 = DailyRecord::factory()->create([
            'stage_id' => $stage->id,
            'batch_id' => $batch->id,
        ]);
        $dailyRecord2 = DailyRecord::factory()->create([
            'stage_id' => $stage->id,
            'batch_id' => $batch->id,
        ]);

        // Create a DailyRecord for a different stage to ensure it's not included
        $otherStage = Stage::factory()->create();
        $otherDailyRecord = DailyRecord::factory()->create([
            'stage_id' => $otherStage->id,
            'batch_id' => $batch->id,
        ]);


        // Assertions
        $this->assertInstanceOf(HasMany::class, $stage->dailyRecords());
        $this->assertCount(2, $stage->dailyRecords); // Access the collection
        $this->assertTrue($stage->dailyRecords->contains($dailyRecord1));
        $this->assertTrue($stage->dailyRecords->contains($dailyRecord2));
        $this->assertFalse($stage->dailyRecords->contains($otherDailyRecord));
    }

    /**
     * Test validation for min_age_days and max_age_days.
     * This is more of a feature/request test if validation is in controller,
     * but if there are model-level constraints or accessors/mutators ensuring this,
     * you could test parts of it here. For now, we'll assume DB constraint or controller validation.
     * The CHECK constraint `max_age_days >= min_age_days` is tested during migration.
     *
     * @return void
     */
    // public function test_stage_age_range_logic()
    // {
    //     // This would typically involve trying to create/update with invalid data
    //     // and expecting an exception or specific model state.
    //     // For example, if you had a mutator or a save event listener.
    // }



    public function it_validates_age_range_boundaries(): void
    {
        $stage = Stage::factory()->create([
            'min_age_days' => 1,
            'max_age_days' => 28
        ]);

        $this->assertTrue($stage->max_age_days >= $stage->min_age_days);
    }

    /**
     * @test
     */
    public function it_cannot_create_stage_with_negative_ages(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        Stage::create([
            'name' => 'Invalid Stage',
            'min_age_days' => -1,
            'max_age_days' => 28
        ]);
    }

}
