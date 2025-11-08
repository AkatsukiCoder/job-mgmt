@extends('layouts.app')

@section('title', 'Edit Job')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Job Posting</h1>
        <a href="{{ route('jobs.index', [], false) }}" class="btn btn-outline-secondary">Back to Jobs</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('jobs.update', ['jobId' => $job['id']], false) }}">
                @csrf
                @method('PUT')

                @include('jobs.partials.form', [
                    'job' => $job,
                    'employmentTypes' => $employmentTypes,
                    'statuses' => $statuses,
                ])

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('jobs.index', [], false) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Job</button>
                </div>
            </form>
        </div>
    </div>
@endsection

