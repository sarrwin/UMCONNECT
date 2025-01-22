@extends('layouts.app')

@section('content')
<div class="container my-4">
    <!-- Page Header -->
    <div class="card shadow border-0 mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0"><i class="fas fa-project-diagram me-2"></i> Project Details</h2>
            <a href="{{ route('supervisor.projects.index') }}" class="btn btn-light text-primary">
                <i class="fas fa-arrow-left"></i> Back to All Projects
            </a>
        </div>
    </div>

    <!-- Project Details Card -->
    <div class="card shadow border-0">
        <div class="card-body p-4">
            <h3 class="text-primary mb-4">{{ $project->title }}</h3>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <tbody>
                        <!-- Description -->
                        <tr>
                            <th class="text-start fw-bold">Description</th>
                            <td>{{ $project->description }}</td>
                        </tr>

                        <!-- Supervisor -->
                        <tr>
                            <th class="text-start fw-bold">Supervisor</th>
                            <td>{{ $project->supervisor->name }}</td>
                        </tr>

                        <!-- Number of Students Required -->
                        <tr>
                            <th class="text-start fw-bold">Number of Students Required</th>
                            <td>{{ $project->students_required }}</td>
                        </tr>

                        <!-- Assigned Students -->
                        <tr>
                            <th class="text-start fw-bold">Assigned Students</th>
                            <td>
                                @if ($project->students->isEmpty())
                                    <span class="text-muted">No students assigned</span>
                                @else
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($project->students as $student)
                                            <span class="badge bg-info text-white">{{ $student->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        </tr>

                        <!-- Session -->
                        <tr>
                            <th class="text-start fw-bold">Session</th>
                            <td>{{ $project->session }}</td>
                        </tr>

                        <!-- Department -->
                        <tr>
                            <th class="text-start fw-bold">Department</th>
                            <td>{{ $project->department }}</td>
                        </tr>

                        <!-- Tools -->
                        <tr>
                            <th class="text-start fw-bold">Tools</th>
                            <td>{{ $project->tools }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    .table th, .table td {
        vertical-align: middle;
        padding: 12px 15px;
    }

    .card {
        border-radius: 15px;
        transition: all 0.3s ease-in-out;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .badge {
        font-size: 0.85rem;
        padding: 6px 12px;
        border-radius: 12px;
    }
</style>
@endsection
