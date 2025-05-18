<?php

namespace Database\Factories;

use App\Modules\Customer\Domain\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'name'        => $this->faker->randomElement(['Casa', 'Trabalho', 'Outros']),
            'street'      => $this->faker->streetName,
            'number'      => $this->faker->buildingNumber,
            'complement'  => $this->faker->optional()->secondaryAddress,
            'district'    => $this->faker->citySuffix,
            'postcode'    => $this->faker->postcode,
            'city'        => $this->faker->city,
            'state'       => $this->faker->stateAbbr,
            'country'     => 'Brasil',
        ];
    }
}
