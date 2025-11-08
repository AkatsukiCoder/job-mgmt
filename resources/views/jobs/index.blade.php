@extends('layouts.app')

@section('title', 'Job Postings')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Job Postings</h1>
        <a href="{{ route('jobs.create', [], false) }}" class="btn btn-primary">Create Job</a>
    </div>

    @if($jobs->isEmpty())
        <div class="alert alert-info">No job postings found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Employment Type</th>
                        <th>Status</th>
                        <th>Posted At</th>
                        <th>Expires At</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jobs as $job)
                        <tr>
                            <td>{{ $job['title'] }}</td>
                            <td>{{ $job['location'] ?? '—' }}</td>
                            <td>{{ $job['employment_type'] }}</td>
                            <td>
                                <span class="badge {{ $job['status'] === 'open' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($job['status']) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($job['posted_at'])->format('Y-m-d H:i') }}</td>
                            <td>{{ \Carbon\Carbon::parse($job['expires_at'])->format('Y-m-d H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('jobs.edit', ['jobId' => $job['id']], false) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $jobs->onEachSide(1)->links('pagination::bootstrap-5') }}
    @endif
@endsection

