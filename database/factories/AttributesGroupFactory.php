<?php

namespace Database\Factories;

use App\Modules\Product\Domain\Models\AttributesGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributesGroupFactory extends Factory
{
    protected $model = AttributesGroup::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
        ];
    }
}
