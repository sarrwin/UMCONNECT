@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-black"><i class="fa fa-book"></i> Logbook for Project: <span class="fw-bold">{{ $project->title }}</span></h1>
        <button class="btn btn-outline-primary" onclick="printLogbook()">
            <i class="fa fa-print"></i> Print Logbook
        </button>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header  text-white" style="background-color: #584f7a;">
            <h5 class="mb-0"><i class="fa fa-filter"></i> Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('supervisor.logbook.index', $project->id) }}" class="row g-3">
                <div class="col-md-4">
                    <label for="studentFilter" class="form-label"><i class="fa fa-user"></i> Filter by Student</label>
                    <select name="student_id" id="studentFilter" class="form-select">
                        <option value="">All Students</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" {{ $request->student_id == $student->id ? 'selected' : '' }}>
                                {{ $student->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="verifiedFilter" class="form-label"><i class="fa fa-check-circle"></i> Filter by Verified Status</label>
                    <select name="verified" id="verifiedFilter" class="form-select">
                        <option value="">All</option>
                        <option value="1" {{ $request->verified === '1' ? 'selected' : '' }}>Verified</option>
                        <option value="0" {{ $request->verified === '0' ? 'selected' : '' }}>Not Verified</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('supervisor.logbook.index', $project->id) }}" class="btn btn-secondary">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Logbook Entries Table -->
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th><i class="fa fa-user"></i> Student Name</th>
                    <th><i class="fa fa-tasks"></i> Activity</th>
                    <th><i class="fa fa-calendar"></i> Date</th>
                    <th><i class="fa fa-check-circle"></i> Verified</th>
                    <th><i class="fa fa-cogs"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($filteredEntries as $entry)
                    <tr>
                        <td>{{ $entry->student->name }}</td>
                        <td>{{ $entry->activity }}</td>
                        <td>{{ \Carbon\Carbon::parse($entry->activity_date)->format('d M Y') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $entry->verified ? 'bg-success' : 'bg-danger' }}">
                                {{ $entry->verified ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                @if ($entry->logbookFiles)
                                    @foreach ($entry->logbookFiles as $file)
                                        <a href="{{ Storage::url($file->file_path) }}" class="btn btn-info btn-sm" target="_blank">
                                            <i class="fa fa-eye"></i> View File
                                        </a>
                                    @endforeach
                                @endif

                                @if (!$entry->verified)
                                    <form action="{{ route('supervisor.logbook.verify', $entry->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fa fa-check"></i> Verify
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('supervisor.logbook.unverify', $entry->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm">
                                            <i class="fa fa-undo"></i> Unverify
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No logbook entries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function printLogbook() {
        window.location.href = "{{ route('logbook.pdf', ['project' => $project->id]) }}";
    }
</script>

<style>
    .table th, .table td {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.5em 1em;
        border-radius: 10px;
    }
    .card-header {
        background-color: #D5C4F3;
        color: #333;
        font-weight: bold;
    }
    .btn {
        font-size: 0.9rem;
    }
</style>
@endsection
