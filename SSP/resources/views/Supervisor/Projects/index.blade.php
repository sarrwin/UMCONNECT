@extends('layouts.app')

@section('content')
<div class="container my-4">
    <!-- Page Header -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #584f7a;">
            <h2 class="fw-bold mb-0"><i class="fas fa-folder-open me-2"></i> Projects</h2>
            <a href="{{ route('supervisor.projects.create') }}" class="btn btn-light text-primary">
                <i class="fas fa-plus-circle"></i> Create New Project
            </a>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="card shadow border-0">
        <div class="card-body p-4">
            @if($projects->isEmpty())
                <div class="alert alert-info text-center">
                    <strong>No projects found.</strong> Start by creating a new project.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr class="text-center">
                                <th>Title</th>
                                <th>Assigned Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                                <tr class="text-center">
                                    <!-- Project Title -->
                                    <td class="fw-bold text-start">
                                        <i class="fas fa-file-alt text-primary me-2"></i>{{ $project->title }}
                                    </td>

                                    <!-- Assigned Students -->
                                    <td>
                                        @if($project->students->isEmpty())
                                            <span class="text-muted">No students assigned</span>
                                        @else
                                            <div class="d-flex justify-content-center flex-wrap gap-2">
                                                @foreach($project->students as $student)
                                                    <span class="badge bg-info text-white">{{ $student->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td>
                                        @if (Auth::id() === $project->supervisor_id)
                                            <div class="d-flex justify-content-center gap-2">

                                            <a href="{{ route('supervisor.projects.details', $project->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i> View Details
                                        </a>
                                                <!-- Edit Button -->
                                                <a href="{{ route('supervisor.projects.edit', $project) }}" class="btn btn-sm btn-secondary">
                                                    <i class="fas fa-pencil-alt"></i> Edit
                                                </a>
                                                <!-- Delete Button -->
                                                <button 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="showDeleteModal({{ $project->id }}, '{{ $project->title }}')"
                                                >
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">⚠️ Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the project <strong id="projectTitle"></strong>?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeleteModal(projectId, projectTitle) {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteForm = document.getElementById('deleteForm');
        const projectTitleElement = document.getElementById('projectTitle');

        // Set the project title in the modal
        projectTitleElement.textContent = projectTitle;

        // Set the action for the delete form
        deleteForm.action = `/supervisor/projects/${projectId}`;

        // Show the modal
        deleteModal.show();
    }
</script>

<style>
    h1.text-primary {
        font-size: 2.5rem;
        font-weight: bold;
    }

    .btn {
        font-size: 0.9rem;
    }

    .badge {
        font-size: 0.875rem;
    }

    .table {
        border: 1px solid #ddd;
        background-color: #f9f9f9;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table-dark th {
        text-transform: uppercase;
        text-align: center;
        font-size: 0.9rem;
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


    .table th, .table td {
        vertical-align: middle;
    }

    .card {
        border-radius: 15px;
        transition: all 0.3s ease-in-out;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .badge {
        font-size: 0.85rem;
        padding: 6px 12px;
        border-radius: 12px;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    .btn-danger:hover {
        background-color: #bd2130;
        border-color: #b21f2d;
    }
</style>
@endsection
