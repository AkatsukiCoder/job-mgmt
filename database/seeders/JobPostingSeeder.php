<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Database\Seeder;

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
