<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'title' => $this->faker->jobTitle,
            'company_id' => Company::factory(),
            'owner_id' => User::factory(),
            'status' => $this->faker->randomElement(['active', 'inactive', 'prospect']),
            'source' => $this->faker->randomElement(['website', 'referral', 'social', 'direct']),
            'last_contacted_at' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function stalled()
    {
        return $this->state(function (array $attributes) {
            return [
                'last_contacted_at' => $this->faker->dateTimeBetween('-3 months', '-31 days'),
            ];
        });
    }

    public function neverContacted()
    {
        return $this->state(function (array $attributes) {
            return [
                'last_contacted_at' => null,
            ];
        });
    }

    public function recentlyContacted()
    {
        return $this->state(function (array $attributes) {
            return [
                'last_contacted_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            ];
        });
    }
}