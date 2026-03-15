<?php

declare(strict_types=1);

namespace Modules\CRM\Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Client, Item};
use Modules\CRM\Models\ReturnOrder;
use Modules\Branches\Models\Branch;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReturnMediaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Client $client;
    protected Item $item;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->user = User::factory()->create();
        $this->client = Client::factory()->create();
        $this->item = Item::factory()->create();
        $this->branch = Branch::factory()->create();

        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_upload_multiple_images_to_return()
    {
        $images = [
            UploadedFile::fake()->image('product1.jpg'),
            UploadedFile::fake()->image('product2.jpg'),
            UploadedFile::fake()->image('product3.jpg'),
        ];

        $response = $this->post(route('returns.store'), [
            'client_id' => $this->client->id,
            'branch_id' => $this->branch->id,
            'return_date' => now()->format('Y-m-d'),
            'return_type' => 'refund',
            'images' => $images,
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 1,
                    'unit_price' => 100,
                ]
            ]
        ]);

        $response->assertRedirect(route('returns.index'));

        $return = ReturnOrder::latest()->first();
        $this->assertCount(3, $return->getMedia('return-images'));
    }

    /** @test */
    public function it_can_upload_pdf_attachment_to_return()
    {
        $pdf = UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf');

        $response = $this->post(route('returns.store'), [
            'client_id' => $this->client->id,
            'branch_id' => $this->branch->id,
            'return_date' => now()->format('Y-m-d'),
            'return_type' => 'refund',
            'attachment' => $pdf,
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 1,
                    'unit_price' => 100,
                ]
            ]
        ]);

        $return = ReturnOrder::latest()->first();
        $this->assertCount(1, $return->getMedia('return-attachments'));
    }

    /** @test */
    public function it_generates_image_conversions()
    {
        $image = UploadedFile::fake()->image('product.jpg');

        $this->post(route('returns.store'), [
            'client_id' => $this->client->id,
            'branch_id' => $this->branch->id,
            'return_date' => now()->format('Y-m-d'),
            'return_type' => 'refund',
            'images' => [$image],
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 1,
                    'unit_price' => 100,
                ]
            ]
        ]);

        $return = ReturnOrder::latest()->first();
        $media = $return->getFirstMedia('return-images');

        $this->assertNotNull($media->getUrl('thumb'));
        $this->assertNotNull($media->getUrl('preview'));
        $this->assertNotNull($media->getUrl('large'));
    }

    /** @test */
    public function it_can_delete_image_from_return()
    {
        $return = ReturnOrder::factory()->create([
            'client_id' => $this->client->id,
            'branch_id' => $this->branch->id,
        ]);

        $image = UploadedFile::fake()->image('product.jpg');
        $media = $return->addMedia($image)->toMediaCollection('return-images');

        $this->assertCount(1, $return->fresh()->getMedia('return-images'));

        $response = $this->get(route('returns.delete-attachment', [
            'return' => $return,
            'mediaId' => $media->id
        ]));

        $this->assertCount(0, $return->fresh()->getMedia('return-images'));
    }

    /** @test */
    public function it_validates_image_file_types()
    {
        $invalidFile = UploadedFile::fake()->create('document.txt', 100);

        $response = $this->post(route('returns.store'), [
            'client_id' => $this->client->id,
            'branch_id' => $this->branch->id,
            'return_date' => now()->format('Y-m-d'),
            'return_type' => 'refund',
            'images' => [$invalidFile],
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 1,
                    'unit_price' => 100,
                ]
            ]
        ]);

        $response->assertSessionHasErrors('images.0');
    }

    /** @test */
    public function it_limits_images_to_five()
    {
        $images = [];
        for ($i = 0; $i < 6; $i++) {
            $images[] = UploadedFile::fake()->image("product{$i}.jpg");
        }

        $response = $this->post(route('returns.store'), [
            'client_id' => $this->client->id,
            'branch_id' => $this->branch->id,
            'return_date' => now()->format('Y-m-d'),
            'return_type' => 'refund',
            'images' => $images,
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'quantity' => 1,
                    'unit_price' => 100,
                ]
            ]
        ]);

        $response->assertSessionHasErrors('images');
    }

    /** @test */
    public function it_can_download_attachment()
    {
        $return = ReturnOrder::factory()->create([
            'client_id' => $this->client->id,
            'branch_id' => $this->branch->id,
        ]);

        $pdf = UploadedFile::fake()->create('invoice.pdf', 100, 'application/pdf');
        $media = $return->addMedia($pdf)->toMediaCollection('return-attachments');

        $response = $this->get(route('returns.download-attachment', [
            'return' => $return,
            'mediaId' => $media->id
        ]));

        $response->assertOk();
        $response->assertDownload();
    }
}
