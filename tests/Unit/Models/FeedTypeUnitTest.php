<?php

namespace Tests\Unit\Models; // Ensure this namespace matches your directory structure

use Tests\TestCase;
use App\Models\FeedType;
use App\Models\FeedRecord;        // For relationship testing
use App\Models\SupplierFeedPrice; // For relationship testing
use App\Models\DailyRecord;       // Dependency for FeedRecord
use App\Models\Batch;             // Dependency for DailyRecord
use App\Models\BirdType;          // Dependency for Batch
use App\Models\Breed;             // Dependency for Batch
use App\Models\Stage;             // Dependency for DailyRecord
use App\Models\Supplier;          // Dependency for SupplierFeedPrice
use App\Models\PurchaseUnit;      // Dependency for SupplierFeedPrice
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedTypeUnitTest extends TestCase
{
    use RefreshDatabase; // This trait resets your database for each test

    /**
     * Test if a FeedType can be created.
     *
     * @return void
     */
    public function test_feed_type_can_be_created(): void
    {
        $feedTypeData = [
            'name' => 'Broiler Starter Mash',
            'description' => 'High protein feed for young broiler chicks.',
        ];

        $feedType = FeedType::create($feedTypeData);

        $this->assertInstanceOf(FeedType::class, $feedType);
        $this->assertEquals($feedTypeData['name'], $feedType->name);
        $this->assertEquals($feedTypeData['description'], $feedType->description);
        $this->assertDatabaseHas('feed_types', $feedTypeData); // Check if it's in the database
    }

    /**
     * Test the fillable attributes of the FeedType model.
     *
     * @return void
     */
    public function test_feed_type_has_correct_fillable_attributes(): void
    {
        $feedType = new FeedType();
        // These should match the $fillable array in your App\Models\FeedType model
        $expectedFillable = ['name', 'description'];
        $this->assertEquals($expectedFillable, $feedType->getFillable());
    }

    /**
     * Test the 'feedRecords' relationship.
     * A FeedType can have many FeedRecords.
     *
     * @return void
     */
    public function test_feed_type_has_many_feed_records_relationship(): void
    {
        // Create a FeedType
        $feedType = FeedType::factory()->create();

        // Create associated DailyRecord and its dependencies (Batch, BirdType, Breed, Stage)
        // This is where factories become very helpful!
        // Ensure you have factories for these models.
        $birdType = BirdType::factory()->create();
        $breed = Breed::factory()->create();
        $batch = Batch::factory()->create(['bird_type_id' => $birdType->id, 'breed_id' => $breed->id]);
        $stage = Stage::factory()->create();
        $dailyRecord = DailyRecord::factory()->create(['batch_id' => $batch->id, 'stage_id' => $stage->id]);

        // Create FeedRecords associated with this FeedType and DailyRecord
        $feedRecord1 = FeedRecord::factory()->create([
            'feed_type_id' => $feedType->id,
            'daily_record_id' => $dailyRecord->id,
        ]);
        $feedRecord2 = FeedRecord::factory()->create([
            'feed_type_id' => $feedType->id,
            'daily_record_id' => $dailyRecord->id,
        ]);

        // Assertions
        $this->assertInstanceOf(HasMany::class, $feedType->feedRecords());
        $this->assertCount(2, $feedType->feedRecords); // Access the collection
        $this->assertTrue($feedType->feedRecords->contains($feedRecord1));
        $this->assertTrue($feedType->feedRecords->contains($feedRecord2));
    }

    /**
     * Test the 'supplierFeedPrices' relationship.
     * A FeedType can have many SupplierFeedPrices.
     *
     * @return void
     */
    public function test_feed_type_has_many_supplier_feed_prices_relationship(): void
    {
        // Create a FeedType
        $feedType = FeedType::factory()->create();

        // Create associated Supplier and PurchaseUnit
        $supplier = Supplier::factory()->create();
        $purchaseUnit = PurchaseUnit::factory()->create();

        // Create SupplierFeedPrices associated with this FeedType
        $price1 = SupplierFeedPrice::factory()->create([
            'feed_type_id' => $feedType->id,
            'supplier_id' => $supplier->id,
            'purchase_unit_id' => $purchaseUnit->id,
        ]);
        $price2 = SupplierFeedPrice::factory()->create([
            'feed_type_id' => $feedType->id,
            'supplier_id' => $supplier->id,
            'purchase_unit_id' => $purchaseUnit->id,
        ]);

        // Assertions
        $this->assertInstanceOf(HasMany::class, $feedType->supplierFeedPrices());
        $this->assertCount(2, $feedType->supplierFeedPrices);
        $this->assertTrue($feedType->supplierFeedPrices->contains($price1));
        $this->assertTrue($feedType->supplierFeedPrices->contains($price2));
    }

    // Add more tests as needed, for example, if you have scopes or accessors/mutators in your FeedType model.
}
