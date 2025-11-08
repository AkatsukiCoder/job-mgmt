<?php

namespace App\Policies;

use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    public function update(User $user, JobPosting $jobPosting): bool
    {
        return $user->id === $jobPosting->created_by;
    }
}
