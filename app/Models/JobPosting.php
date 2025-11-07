<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'location',
        'employment_type',
        'description',
        'salary_min',
        'salary_max',
        'currency',
        'status',
        'posted_at',
        'expires_at',
        'created_by',
    ];
}
