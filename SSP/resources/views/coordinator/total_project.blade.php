@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Page Title -->
    <h1 class="text-center text-primary mb-4">📂 Department's Projects</h1>

    <!-- Table Section -->
    <div class="table-responsive shadow-lg p-4 bg-white rounded">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>Supervisor</th>
                    <th>Project Title</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr>
                    <td>{{ $project->supervisor->name ?? 'No Supervisor Assigned' }}</td>
                    <td>{{ $project->title }}</td>
                    <td>{{ $project->department }}</td>
                    <td class="text-center">
                        <!-- Button to trigger modal -->
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#projectDetailsModal-{{ $project->id }}">
                            <i class="fa fa-eye"></i> View
                        </button>
                    </td>
                </tr>

                <!-- Project Details Modal -->
                <div class="modal fade" id="projectDetailsModal-{{ $project->id }}" tabindex="-1" aria-labelledby="projectDetailsModalLabel-{{ $project->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="projectDetailsModalLabel-{{ $project->id }}">Project Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Title:</strong> {{ $project->title }}</p>
                                <p><strong>Supervisor:</strong> {{ $project->supervisor->name ?? 'No Supervisor Assigned' }}</p>
                                <p><strong>Department:</strong> {{ $project->department }}</p>
                                <p><strong>Description:</strong> {{ $project->description ?? 'No Description Available' }}</p>
                                <p><strong>Tools:</strong> {{ $project->tools ?? 'Not Specified' }}</p>
                                <p><strong>Students Required:</strong> {{ $project->students_required }}</p>
                                <p><strong>Session:</strong> {{ $project->session }}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No projects found in this department.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .table {
        border: 1px solid #ddd;
        background-color: #f9f9f9;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table thead th {
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
    }

    h1.text-primary {
        font-size: 2.5rem;
        font-weight: bold;
    }

    .shadow-lg {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .bg-white {
        background-color: #fff;
    }

    .rounded {
        border-radius: 10px;
    }

    .modal-header {
        border-bottom: 1px solid #ddd;
    }

    .modal-footer {
        border-top: 1px solid #ddd;
    }

    .btn-sm {
        font-size: 0.875rem;
    }
</style>
@endsection
