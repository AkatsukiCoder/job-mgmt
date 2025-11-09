<?php

namespace App\Http\Controllers;

use App\Models\JobPosting;
use App\Services\ApiClient;
use App\Services\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class JobPageController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    public function index(Request $request)
    {
        if ($redirect = $this->redirectIfNoToken($request)) {
            return $redirect;
        }

        $page = (int) $request->query('page', 1);

        $response = $this->api->get('/api/jobs', ['page' => $page], $request->session()->get('api_token'));

        if ($redirect = $this->handleAuthFailure($response, $request)) {
            return $redirect;
        }

        $payload = $response->json();

        $paginator = new LengthAwarePaginator(
            Arr::get($payload, 'data', []),
            Arr::get($payload, 'total', 0),
            Arr::get($payload, 'per_page', 20),
            Arr::get($payload, 'current_page', 1),
            ['path' => route('jobs.index', [], false)]
        );

        return view('jobs.index', [
            'jobs' => $paginator,
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->redirectIfNoToken($request)) {
            return $redirect;
        }

        return view('jobs.create', [
            'employmentTypes' => JobPosting::EMPLOYMENT_TYPES,
            'statuses' => JobPosting::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->redirectIfNoToken($request)) {
            return $redirect;
        }

        $data = $this->validateJobData($request);

        $response = $this->api->post('/api/jobs', $data, $request->session()->get('api_token'));

        if ($redirect = $this->handleAuthFailure($response, $request)) {
            return $redirect;
        }

        if ($response->status() === 422) {
            return $this->handleValidationErrors($response, $request, 'create');
        }

        if ($response->failed()) {
            return back()
                ->withErrors(['general' => $this->extractErrorMessage($response, 'Failed to create job posting. Please try again.')])
                ->withInput();
        }

        return redirect('/jobs')
            ->with('status', 'Job posting created successfully.');
    }

    public function edit(Request $request, int $jobId)
    {
        if ($redirect = $this->redirectIfNoToken($request)) {
            return $redirect;
        }

        $response = $this->api->get("/api/jobs/{$jobId}", [], $request->session()->get('api_token'));

        if ($redirect = $this->handleAuthFailure($response, $request)) {
            return $redirect;
        }

        if ($response->notFound()) {
            return redirect('/jobs')
                ->withErrors(['general' => 'Job posting not found.']);
        }

        if ($response->failed()) {
            return redirect('/jobs')
                ->withErrors(['general' => 'Unable to load job posting.']);
        }

        $job = $response->json();

        return view('jobs.edit', [
            'job' => $job,
            'employmentTypes' => JobPosting::EMPLOYMENT_TYPES,
            'statuses' => JobPosting::STATUSES,
        ]);
    }

    public function update(Request $request, int $jobId): RedirectResponse
    {
        if ($redirect = $this->redirectIfNoToken($request)) {
            return $redirect;
        }

        $data = $this->validateJobData($request);

        $response = $this->api->put("/api/jobs/{$jobId}", $data, $request->session()->get('api_token'));

        if ($redirect = $this->handleAuthFailure($response, $request)) {
            return $redirect;
        }

        if ($response->status() === 422) {
            return $this->handleValidationErrors($response, $request, 'edit', $jobId);
        }

        if ($response->status() === 403) {
            return back()
                ->withErrors(['general' => $this->extractErrorMessage($response, 'You are not authorized to update this job posting.')])
                ->withInput();
        }

        if ($response->failed()) {
            return back()
                ->withErrors(['general' => $this->extractErrorMessage($response, 'Failed to update job posting. Please try again.')])
                ->withInput();
        }

        return redirect('/jobs')
            ->with('status', 'Job posting updated successfully.');
    }

    protected function validateJobData(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'in:'.implode(',', JobPosting::EMPLOYMENT_TYPES)],
            'description' => ['nullable', 'string'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'gte:salary_min'],
            'currency' => ['required', 'string', 'max:10'],
            'status' => ['nullable', 'in:'.implode(',', JobPosting::STATUSES)],
            'posted_at' => ['required', 'date'],
            'expires_at' => ['required', 'date', 'after_or_equal:posted_at'],
        ]);

        return [
            'title' => $validated['title'],
            'location' => $validated['location'] ?? null,
            'employment_type' => $validated['employment_type'],
            'description' => $validated['description'] ?? null,
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'currency' => $validated['currency'],
            'status' => $validated['status'] ?? null,
            'posted_at' => $this->formatDateTime($validated['posted_at']),
            'expires_at' => $this->formatDateTime($validated['expires_at']),
        ];
    }

    protected function formatDateTime(string $value): string
    {
        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    protected function redirectIfNoToken(Request $request): ?RedirectResponse
    {
        if ($request->session()->missing('api_token')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Please log in to continue.']);
        }

        return null;
    }

    protected function handleAuthFailure(ApiResponse $response, Request $request): ?RedirectResponse
    {
        if ($response->unauthorized()) {
            $request->session()->forget('api_token');

            return redirect('/login')
                ->withErrors(['email' => 'Your session has expired. Please log in again.']);
        }

        return null;
    }

    protected function handleValidationErrors(
        ApiResponse $response,
        Request $request,
        string $context,
        ?int $jobId = null
    ): RedirectResponse {
        $errorBag = $response->json('errors', []);

        $flattened = collect($errorBag)
            ->map(fn ($messages) => is_array($messages) ? $messages : [$messages])
            ->map(fn ($messages) => implode(' ', $messages))
            ->all();

        $route = $context === 'edit'
            ? route('jobs.edit', ['jobId' => $jobId], false)
            : route('jobs.create', [], false);

        return redirect($route)
            ->withErrors($flattened)
            ->withInput();
    }

    protected function extractErrorMessage(ApiResponse $response, string $default): string
    {
        $message = $response->json('message');

        if (is_string($message) && $message !== '') {
            return $message;
        }

        $errors = $response->json('errors');

        if (is_array($errors)) {
            foreach ($errors as $error) {
                if (is_array($error)) {
                    $first = reset($error);
                    if (is_string($first) && $first !== '') {
                        return $first;
                    }
                } elseif (is_string($error) && $error !== '') {
                    return $error;
                }
            }
        }

        return $default;
    }
}
