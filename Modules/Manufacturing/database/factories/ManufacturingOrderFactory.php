<?php

declare(strict_types=1);

namespace Modules\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Manufacturing\Models\ManufacturingOrder;

class ManufacturingOrderFactory extends Factory
{
    protected $model = ManufacturingOrder::class;

    public function definition(): array
    {
        return [
            'order_number'       => ManufacturingOrder::generateOrderNumber(),
            'template_name'      => $this->faker->words(3, true),
            'branch_id'          => 1,
            'status'             => 'draft',
            'description'        => $this->faker->optional()->sentence,
            'item_id'            => null,
            'estimated_duration' => $this->faker->randomFloat(2, 1, 10),
            'is_template'        => false,
        ];
    }

    public function template(): static
    {
        return $this->state(['is_template' => true]);
    }
}
