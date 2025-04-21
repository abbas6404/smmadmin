<?php

namespace Database\Factories;

use App\Models\PcProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class PcProfileFactory extends Factory
{
    protected $model = PcProfile::class;

    public function definition()
    {
        $osVersions = [
            'Windows 10 Home',
            'Windows 10 Pro',
            'Windows 11 Home',
            'Windows 11 Pro',
        ];

        return [
            'name' => $this->faker->word . '-PC',
            'hostname' => strtoupper($this->faker->word . '-' . $this->faker->numberBetween(1000, 9999)),
            'os_version' => $this->faker->randomElement($osVersions),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'max_chrome_profiles' => $this->faker->numberBetween(3, 10),
            'max_gmail_accounts' => $this->faker->numberBetween(3, 10),
            'max_facebook_accounts' => $this->faker->numberBetween(3, 10),
            'last_used_at' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
                'last_used_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
            ];
        });
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'inactive',
                'last_used_at' => $this->faker->dateTimeBetween('-1 month', '-1 week'),
            ];
        });
    }
} 