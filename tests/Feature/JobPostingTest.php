<?php

namespace Tests\Feature;

use App\Models\JobPosting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPostingTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_jobs()
    {
        JobPosting::factory()->count(3)->create();

        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/jobs');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_create_job()
    {
        $user = User::factory()->create();

        $payload = [
            'title' => 'Developer',
            'employment_type' => 'Full-time',
            'status' => 'open',
            'posted_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'expires_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/jobs', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['title' => 'Developer']);

        $this->assertDatabaseHas('job_postings', ['title' => 'Developer']);
    }
}
