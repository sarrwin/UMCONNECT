@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Card Header for Project Details -->
    <div class="card shadow-lg mb-5">
        <div class="card-header bg-primary text-white text-center">
            <h1 class="card-title">📘 Logbook for Project</h1>
            <h3 class="card-subtitle mt-2">Project: <strong>{{ $project->title }}</strong></h3>
            <h4 class="card-subtitle mt-1">Supervisor: <strong>{{ $project->supervisor->name }}</strong></h4>
        </div>
    </div>

    <!-- Logbook Entries -->
    @forelse ($project->logbooks as $logbook)
        @if ($logbook->entries->isNotEmpty())
            <div class="card shadow-lg mb-4">
                <div class="card-body">
                    <h5 class="text-secondary">Logbook Entries</h5>
                    <table class="table table-hover table-striped align-middle mt-3">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th>Student</th>
                                <th>Activity</th>
                                <th>Date</th>
                                <th>Files</th>
                                <th>Verified</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logbook->entries as $entry)
                                <tr>
                                    <td>{{ $entry->student->name }}</td>
                                    <td>{{ $entry->activity }}</td>
                                    <td>{{ \Carbon\Carbon::parse($entry->activity_date)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        @forelse ($entry->logbookFiles as $file)
                                            <a href="{{ Storage::url($file->file_path) }}" class="btn btn-sm btn-info" target="_blank">
                                                <i class="fa fa-file"></i> File
                                            </a>
                                        @empty
                                            <span class="text-muted">No Files</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $entry->verified ? 'bg-success' : 'bg-danger' }}">
                                            {{ $entry->verified ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="alert alert-warning text-center">
                <strong>No entries found for this logbook.</strong>
            </div>
        @endif
    @empty
        <div class="alert alert-info text-center">
            <strong>No logbooks available for this project.</strong>
        </div>
    @endforelse
</div>

<style>
    .card-header {
        border-bottom: 1px solid #ddd;
    }

    .card-title {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0;
    }

    .card-subtitle {
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table-dark th {
        text-transform: uppercase;
        text-align: center;
        font-size: 0.9rem;
    }

    .badge {
        font-size: 0.875rem;
        padding: 0.5em 1em;
    }

    .btn-sm {
        font-size: 0.875rem;
    }

    .shadow-lg {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .alert {
        font-size: 1rem;
    }
    .card:hover {
    transform: none !important; /* Prevents moving up */
    box-shadow: none !important; /* Prevents shadow change */
}
</style>
@endsection
