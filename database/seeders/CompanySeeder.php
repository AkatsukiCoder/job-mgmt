<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'TechNova Solutions',
                'about' => 'A fast-growing tech startup specializing in AI-driven enterprise solutions.',
            ],
            [
                'name' => 'GreenWave Logistics',
                'about' => 'Sustainable logistics company focused on reducing carbon emissions in transport.',
            ],
            [
                'name' => 'FinEdge Capital',
                'about' => 'Financial services firm offering innovative investment solutions and digital banking tools.',
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
