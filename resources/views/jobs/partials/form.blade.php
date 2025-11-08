@php
    $job = $job ?? null;
    $postedAtValue = old('posted_at') ?? ($job['posted_at'] ?? null);
    $expiresAtValue = old('expires_at') ?? ($job['expires_at'] ?? null);

    $formatForInput = function (?string $value, ?string $fallback = null): string {
        if ($value) {
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i');
            } catch (\Exception) {
                return $value;
            }
        }

        if ($fallback) {
            return $fallback;
        }

        return '';
    };

    $postedAtDefault = $formatForInput($postedAtValue, now()->addDay()->format('Y-m-d\TH:i'));
    $expiresAtDefault = $formatForInput($expiresAtValue, now()->addDays(8)->format('Y-m-d\TH:i'));
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label for="title" class="form-label">Title</label>
        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
               value="{{ old('title', $job['title'] ?? '') }}" required>
        @error('title')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="location" class="form-label">Location</label>
        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror"
               value="{{ old('location', $job['location'] ?? '') }}">
        @error('location')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="employment_type" class="form-label">Employment Type</label>
        <select id="employment_type" name="employment_type" class="form-select @error('employment_type') is-invalid @enderror" required>
            <option value="">Select type</option>
            @foreach($employmentTypes as $type)
                <option value="{{ $type }}"
                    @selected(old('employment_type', $job['employment_type'] ?? '') === $type)>
                    {{ $type }}
                </option>
            @endforeach
        </select>
        @error('employment_type')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
            <option value="">Select status</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}"
                    @selected(old('status', $job['status'] ?? '') === $status)>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="salary_min" class="form-label">Salary Min</label>
        <input type="number" step="0.01" id="salary_min" name="salary_min" class="form-control @error('salary_min') is-invalid @enderror"
               value="{{ old('salary_min', $job['salary_min'] ?? '') }}">
        @error('salary_min')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="salary_max" class="form-label">Salary Max</label>
        <input type="number" step="0.01" id="salary_max" name="salary_max" class="form-control @error('salary_max') is-invalid @enderror"
               value="{{ old('salary_max', $job['salary_max'] ?? '') }}">
        @error('salary_max')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="currency" class="form-label">Currency</label>
        <input type="text" id="currency" name="currency" class="form-control @error('currency') is-invalid @enderror"
               value="{{ old('currency', $job['currency'] ?? '') }}" maxlength="10" placeholder="MYR" required>
        @error('currency')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="posted_at" class="form-label">Posted At</label>
        <input type="datetime-local" id="posted_at" name="posted_at" class="form-control @error('posted_at') is-invalid @enderror"
               value="{{ $postedAtDefault }}" required>
        @error('posted_at')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="expires_at" class="form-label">Expires At</label>
        <input type="datetime-local" id="expires_at" name="expires_at" class="form-control @error('expires_at') is-invalid @enderror"
               value="{{ $expiresAtDefault }}" required>
        @error('expires_at')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description', $job['description'] ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>

