<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobPostingSeeder extends Seeder
{
    public function run(): void
    {

        $user = User::first() ?? User::factory()->create([
            'name' => 'John Recruiter',
            'email' => 'recruiter@example.com',
        ]);

        JobPosting::factory()->count(5)->create([
            'created_by' => $user->id,
        ]);
    }
}
