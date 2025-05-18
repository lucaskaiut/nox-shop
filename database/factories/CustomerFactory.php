<?php

namespace Database\Factories;

use App\Modules\Customer\Domain\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'email'      => $this->faker->unique()->safeEmail,
            'password'   => bcrypt('secret123'),
            'type'       => 'person',
            'document'   => $this->faker->cpf(false),
            'birthdate'  => $this->faker->date('Y-m-d', '-18 years'),
        ];
    }
}
