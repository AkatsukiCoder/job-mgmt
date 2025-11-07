<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobPosting;
use App\Models\Company;
use App\Models\User;

class JobPostingSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first() ?? Company::factory()->create([
            'name' => 'Example Company',
        ]);

        $user = User::first() ?? User::factory()->create([
            'name' => 'John Recruiter',
            'email' => 'recruiter@example.com',
            'company_id' => $company->id,
        ]);

        JobPosting::factory()->count(5)->create([
            'created_by' => $user->id,
        ]);
    }
}
