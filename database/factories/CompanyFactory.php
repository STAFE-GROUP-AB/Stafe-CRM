<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'slug' => $this->faker->slug,
            'website' => $this->faker->optional()->url,
            'email' => $this->faker->optional()->companyEmail,
            'phone' => $this->faker->optional()->phoneNumber,
            'industry' => $this->faker->randomElement(['Technology', 'Healthcare', 'Finance', 'Manufacturing', 'Retail']),
            'employee_count' => $this->faker->numberBetween(1, 10000),
            'annual_revenue' => $this->faker->optional()->randomFloat(2, 100000, 10000000),
            'currency' => 'USD',
            'address' => $this->faker->optional()->streetAddress,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'country' => $this->faker->country,
            'timezone' => $this->faker->timezone,
            'description' => $this->faker->optional()->paragraph,
            'status' => $this->faker->randomElement(['active', 'inactive', 'prospect']),
            'owner_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}