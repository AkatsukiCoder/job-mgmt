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

    public const FULL_TIME = 'Full-time';

    public const PART_TIME = 'Part-time';

    public const CONTRACT = 'Contract';

    public const INTERNSHIP = 'Internship';

    public const EMPLOYMENT_TYPES = [
        self::FULL_TIME,
        self::PART_TIME,
        self::CONTRACT,
        self::INTERNSHIP,
    ];

    const STATUS_OPEN = 'open';

    const STATUS_CLOSED = 'closed';

    const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_CLOSED,
    ];
}
