<?php

namespace Tests\Unit\Models; // Correct namespace

use Tests\TestCase; // Use the base TestCase
use App\Models\Breed; // Import the model we are testing
use App\Models\Batch; // Import related model for relationship test
use Illuminate\Foundation\Testing\RefreshDatabase; // Use for database interactions
use Illuminate\Database\Eloquent\Relations\HasMany; // For relationship assertion

class BreedUnitTest extends TestCase
{
    use RefreshDatabase; // Automatically migrate and reset the test database

    /**
     * Test if a Breed can be created using the factory or attributes.
     *
     * @return void
     */
    public function test_breed_can_be_created(): void
    {
        // Method 1: Using attributes (if no factory exists)
        $breedData = [
            'name' => 'TestRoss 308',
            'description' => 'A test broiler breed.',
        ];
        $breed = Breed::create($breedData);

        // Assert: Check if the breed was actually created in the database
        $this->assertDatabaseHas('breeds', [ // Check the 'breeds' table
            'name' => 'TestRoss 308'
        ]);
        $this->assertInstanceOf(Breed::class, $breed);
        $this->assertEquals('TestRoss 308', $breed->name);

        // Method 2: Using a Factory (Recommended)
        // If you haven't created a factory yet, run:
        // php artisan make:factory BreedFactory --model=Breed
        // Then define default attributes in database/factories/BreedFactory.php
        // $factoryBreed = Breed::factory()->create();
        // $this->assertDatabaseHas('breeds', ['id' => $factoryBreed->id]);
        // $this->assertInstanceOf(Breed::class, $factoryBreed);
    }

    /**
     * Test the fillable attributes of the Breed model.
     * Ensures only expected attributes can be mass-assigned.
     *
     * @return void
     */
    public function test_breed_has_correct_fillable_attributes(): void
    {
        $breed = new Breed();
        $expectedFillable = ['name', 'description']; // Match $fillable in Breed model
        $this->assertEquals($expectedFillable, $breed->getFillable());
    }

    /**
     * Test the relationship between Breed and Batch.
     * A Breed should have many Batches.
     *
     * @return void
     */
    public function test_breed_has_many_batches_relationship(): void
    {
        // Create a Breed instance (using factory is cleaner if available)
        $breed = Breed::create(['name' => 'TestBreedForRelation']);

        // Create related Batch instances associated with this Breed
        // Need BirdType as well for Batch creation
        $birdType = \App\Models\BirdType::factory()->create(); // Assuming BirdType factory exists
        $batch1 = Batch::factory()->create([
            'breed_id' => $breed->id,
            'bird_type_id' => $birdType->id,
        ]);
        $batch2 = Batch::factory()->create([
            'breed_id' => $breed->id,
            'bird_type_id' => $birdType->id,
        ]);
        $otherBreed = Breed::factory()->create();
        $otherBatch = Batch::factory()->create([
            'breed_id' => $otherBreed->id,
            'bird_type_id' => $birdType->id,
        ]);


        // Assert: Check the relationship type
        $this->assertInstanceOf(HasMany::class, $breed->batches());

        // Assert: Check the count of related batches
        $this->assertCount(2, $breed->batches); // Access the loaded collection

        // Assert: Check if the related batches are the correct instances
        $this->assertTrue($breed->batches->contains($batch1));
        $this->assertTrue($breed->batches->contains($batch2));
        $this->assertFalse($breed->batches->contains($otherBatch));
    }

    /**
     * Test attribute casting.
     * Example: If you had an 'is_active' boolean field.
     *
     * @return void
     */
    // public function test_breed_attribute_casting(): void
    // {
    //     $breed = Breed::factory()->create(['is_active' => 1]); // Assuming factory can set this
    //     $this->assertIsBool($breed->is_active); // Check if it's cast to boolean
    //     $this->assertTrue($breed->is_active);

    //     $breedInactive = Breed::factory()->create(['is_active' => 0]);
    //     $this->assertIsBool($breedInactive->is_active);
    //     $this->assertFalse($breedInactive->is_active);
    // }

}
// End of BreedUnitTest.php
