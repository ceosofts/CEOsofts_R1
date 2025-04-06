<?php

namespace Database\Factories;

use App\Domain\Organization\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid(),
            'name' => $this->faker->company(),
            'code' => $this->faker->unique()->bothify('COMP-###'),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->companyEmail(),
            'tax_id' => $this->faker->numerify('##########'),
            'website' => $this->faker->url(),
            'logo' => null,
            'is_active' => true,
            'status' => 'active',
            'settings' => null,
            'metadata' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
