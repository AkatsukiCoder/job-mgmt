<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobPostingRequest;
use App\Models\JobPosting;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="JobPostings",
 *     description="Endpoints for managing job postings"
 * )
 */
class JobPostingController extends Controller
{
    /**
     * Get all job postings with pagination.
     *
     * @OA\Get(
     *     path="/api/jobs",
     *     tags={"JobPostings"},
     *     summary="Get all job postings (paginated)",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of job postings",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Software Engineer"),
     *                     @OA\Property(property="location", type="string", example="Kuala Lumpur"),
     *                     @OA\Property(property="employment_type", type="string", example="Full-time"),
     *                     @OA\Property(property="description", type="string", example="Job description here"),
     *                     @OA\Property(property="salary_min", type="number", format="float", example=3000),
     *                     @OA\Property(property="salary_max", type="number", format="float", example=5000),
     *                     @OA\Property(property="currency", type="string", example="MYR"),
     *                     @OA\Property(property="status", type="string", example="open"),
     *                     @OA\Property(property="posted_at", type="string", format="date-time", example="2025-11-08 10:00:00"),
     *                     @OA\Property(property="expires_at", type="string", format="date-time", example="2025-12-08 10:00:00"),
     *                     @OA\Property(property="created_by", type="integer", example=1)
     *                 )
     *             ),
     *             @OA\Property(property="first_page_url", type="string", example="http://localhost/api/jobs?page=1"),
     *             @OA\Property(property="from", type="integer", example=1),
     *             @OA\Property(property="last_page", type="integer", example=5),
     *             @OA\Property(property="last_page_url", type="string", example="http://localhost/api/jobs?page=5"),
     *             @OA\Property(property="next_page_url", type="string", example="http://localhost/api/jobs?page=2"),
     *             @OA\Property(property="path", type="string", example="http://localhost/api/jobs"),
     *             @OA\Property(property="per_page", type="integer", example=20),
     *             @OA\Property(property="prev_page_url", type="string", example=null),
     *             @OA\Property(property="to", type="integer", example=20),
     *             @OA\Property(property="total", type="integer", example=100)
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $defaultPerPage = 20;

        return response()->json(JobPosting::paginate($defaultPerPage));
    }

    /**
     * Create a new job posting.
     *
     * @OA\Post(
     *     path="/api/jobs",
     *     tags={"JobPostings"},
     *     summary="Create a new job posting",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *             required={"title","employment_type","status","posted_at"},
     *
     *             @OA\Property(property="title", type="string", description="Job title", example="Senior Developer"),
     *             @OA\Property(property="location", type="string", description="Job location", example="Kuala Lumpur"),
     *             @OA\Property(
     *                 property="employment_type",
     *                 type="string",
     *                 description="Type of employment",
     *                 enum={"Full-time","Part-time","Contract","Internship"},
     *                 example="Full-time"
     *             ),
     *             @OA\Property(property="description", type="string", description="Job description", example="Develop and maintain web applications."),
     *             @OA\Property(property="salary_min", type="number", format="float", description="Minimum salary", example=5000),
     *             @OA\Property(property="salary_max", type="number", format="float", description="Maximum salary", example=8000),
     *             @OA\Property(property="currency", type="string", description="Currency code, e.g., USD, MYR", example="MYR"),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="Job status",
     *                 enum={"open","closed"},
     *                 example="open"
     *             ),
     *             @OA\Property(
     *                 property="posted_at",
     *                 type="string",
     *                 description="Posting date (Y-m-d H:i:s), must be in the future",
     *                 example="2025-11-08 16:46:32"
     *             ),
     *             @OA\Property(
     *                 property="expires_at",
     *                 type="string",
     *                 description="Expiration date (Y-m-d H:i:s)",
     *                 example="2025-11-15 16:46:32"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Job posting created successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Job posting created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Senior Developer"),
     *                 @OA\Property(property="location", type="string", example="Kuala Lumpur"),
     *                 @OA\Property(property="employment_type", type="string", example="Full-time"),
     *                 @OA\Property(property="description", type="string", example="Develop and maintain web applications."),
     *                 @OA\Property(property="salary_min", type="number", format="float", example=5000),
     *                 @OA\Property(property="salary_max", type="number", format="float", example=8000),
     *                 @OA\Property(property="currency", type="string", example="MYR"),
     *                 @OA\Property(property="status", type="string", example="open"),
     *                 @OA\Property(property="posted_at", type="string", example="2025-11-08 16:46:32"),
     *                 @OA\Property(property="expires_at", type="string", example="2025-11-15 16:46:32"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreJobPostingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->user()->id;
        $job = JobPosting::create($data);

        return response()->json([
            'message' => 'Job posting created successfully.',
            'data' => $job,
        ], 201);
    }

    /**
     * Get a specific job posting.
     *
     * @OA\Get(
     *     path="/api/jobs/{id}",
     *     tags={"JobPostings"},
     *     summary="Get a specific job posting",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the job posting",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Job posting details",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="employment_type", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="salary_min", type="number", format="float"),
     *             @OA\Property(property="salary_max", type="number", format="float"),
     *             @OA\Property(property="currency", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="posted_at", type="string", format="date-time"),
     *             @OA\Property(property="expires_at", type="string", format="date-time"),
     *             @OA\Property(property="created_by", type="integer")
     *         )
     *     ),
     *
     *     @OA\Response(response=404, description="Job posting not found")
     * )
     */
    public function show(JobPosting $jobPosting): JsonResponse
    {
        return response()->json($jobPosting);
    }

    /**
     * Update a specific job posting.
     *
     * @OA\Put(
     *     path="/api/jobs/{id}",
     *     tags={"JobPostings"},
     *     summary="Update a job posting",
     *     security={{"bearerAuth": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the job posting",
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             type="object",
     *             required={"title","employment_type"},
     *
     *             @OA\Property(property="title", type="string", example="Senior Developer"),
     *             @OA\Property(property="location", type="string", example="Kuala Lumpur"),
     *             @OA\Property(
     *                 property="employment_type",
     *                 type="string",
     *                 enum={"Full-time","Part-time","Contract","Internship"},
     *                 description="Type of employment",
     *                 example="Full-time"
     *             ),
     *             @OA\Property(property="description", type="string", example="Job description here."),
     *             @OA\Property(property="salary_min", type="number", format="float", example=5000),
     *             @OA\Property(property="salary_max", type="number", format="float", example=8000),
     *             @OA\Property(property="currency", type="string", example="MYR"),
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 enum={"open","closed"},
     *                 description="Job status",
     *                 example="open"
     *             ),
     *             @OA\Property(
     *                 property="posted_at",
     *                 type="string",
     *                 description="Posting date (Y-m-d H:i:s), must be in the future",
     *                 example="2025-11-30 16:46:32"
     *             ),
     *             @OA\Property(
     *                 property="expires_at",
     *                 type="string",
     *                 description="Expiration date (Y-m-d H:i:s)",
     *                 example="2025-12-10 16:46:32"
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Job posting updated successfully",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Job posting updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Senior Developer"),
     *                 @OA\Property(property="location", type="string", example="Kuala Lumpur"),
     *                 @OA\Property(property="employment_type", type="string", example="Full-time"),
     *                 @OA\Property(property="description", type="string", example="Job description here."),
     *                 @OA\Property(property="salary_min", type="number", format="float", example=5000),
     *                 @OA\Property(property="salary_max", type="number", format="float", example=8000),
     *                 @OA\Property(property="currency", type="string", example="MYR"),
     *                 @OA\Property(property="status", type="string", example="open"),
     *                 @OA\Property(property="posted_at", type="string", example="2025-11-08 16:46:32"),
     *                 @OA\Property(property="expires_at", type="string", example="2025-11-15 16:46:32"),
     *                 @OA\Property(property="created_by", type="integer", example=1)
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Job posting not found")
     * )
     */
    public function update(StoreJobPostingRequest $request, JobPosting $jobPosting): JsonResponse
    {
        $this->authorize('update', $jobPosting);

        $jobPosting->update($request->validated());

        return response()->json([
            'message' => 'Job posting updated successfully.',
            'data' => $jobPosting,
        ]);
    }
}
