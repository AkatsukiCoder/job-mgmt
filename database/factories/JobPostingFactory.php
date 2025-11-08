<?php

namespace Database\Factories;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobPostingFactory extends Factory
{
    protected $model = JobPosting::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'location' => $this->faker->city(),
            'employment_type' => $this->faker->randomElement(JobPosting::EMPLOYMENT_TYPES),
            'description' => $this->faker->paragraphs(3, true),
            'salary_min' => $this->faker->numberBetween(3000, 6000),
            'salary_max' => $this->faker->numberBetween(7000, 12000),
            'currency' => 'MYR',
            'status' => $this->faker->randomElement(JobPosting::STATUSES),
            'posted_at' => now()->subDays(rand(1, 30)),
            'expires_at' => now()->addDays(rand(10, 60)),
            'created_by' => User::factory(),
        ];
    }
}
