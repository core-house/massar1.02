<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemImageUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function item_can_have_thumbnail_image(): void
    {
        $item = Item::factory()->create([
            'name' => 'Test Item',
            'code' => 1001,
            'type' => 1,
        ]);

        $file = UploadedFile::fake()->image('thumbnail.jpg', 800, 600);

        $item->addMedia($file)
            ->toMediaCollection('item-thumbnail');

        $this->assertCount(1, $item->getMedia('item-thumbnail'));
        $this->assertEquals('thumbnail.jpg', $item->getFirstMedia('item-thumbnail')->file_name);
    }

    /** @test */
    public function item_can_have_multiple_images(): void
    {
        $item = Item::factory()->create([
            'name' => 'Test Item',
            'code' => 1002,
            'type' => 1,
        ]);

        $file1 = UploadedFile::fake()->image('image1.jpg');
        $file2 = UploadedFile::fake()->image('image2.jpg');
        $file3 = UploadedFile::fake()->image('image3.jpg');

        $item->addMedia($file1)->toMediaCollection('item-images');
        $item->addMedia($file2)->toMediaCollection('item-images');
        $item->addMedia($file3)->toMediaCollection('item-images');

        $this->assertCount(3, $item->getMedia('item-images'));
    }

    /** @test */
    public function item_thumbnail_is_single_file(): void
    {
        $item = Item::factory()->create([
            'name' => 'Test Item',
            'code' => 1003,
            'type' => 1,
        ]);

        $file1 = UploadedFile::fake()->image('thumbnail1.jpg');
        $file2 = UploadedFile::fake()->image('thumbnail2.jpg');

        $item->addMedia($file1)->toMediaCollection('item-thumbnail');
        $item->addMedia($file2)->toMediaCollection('item-thumbnail');

        // Should only have one thumbnail (single file collection)
        $this->assertCount(1, $item->getMedia('item-thumbnail'));
        $this->assertEquals('thumbnail2.jpg', $item->getFirstMedia('item-thumbnail')->file_name);
    }

    /** @test */
    public function item_image_conversions_are_generated(): void
    {
        $item = Item::factory()->create([
            'name' => 'Test Item',
            'code' => 1004,
            'type' => 1,
        ]);

        $file = UploadedFile::fake()->image('test.jpg', 1000, 1000);

        $media = $item->addMedia($file)->toMediaCollection('item-thumbnail');

        // Check that conversions exist
        $this->assertNotNull($media->getPath('thumb'));
        $this->assertNotNull($media->getPath('preview'));
        $this->assertNotNull($media->getPath('large'));
    }

    /** @test */
    public function deleting_item_deletes_its_images(): void
    {
        $item = Item::factory()->create([
            'name' => 'Test Item',
            'code' => 1005,
            'type' => 1,
        ]);

        $file = UploadedFile::fake()->image('thumbnail.jpg');
        $item->addMedia($file)->toMediaCollection('item-thumbnail');

        $mediaId = $item->getFirstMedia('item-thumbnail')->id;

        $item->delete();

        $this->assertDatabaseMissing('media', ['id' => $mediaId]);
    }

    /** @test */
    public function item_returns_fallback_url_when_no_image(): void
    {
        $item = Item::factory()->create([
            'name' => 'Test Item',
            'code' => 1006,
            'type' => 1,
        ]);

        $url = $item->getFirstMediaUrl('item-thumbnail');

        $this->assertStringContainsString('no-image.png', $url);
    }
}

