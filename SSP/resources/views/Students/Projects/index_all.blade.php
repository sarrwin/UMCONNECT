@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Card Wrapper -->
    <div class="card shadow-lg border-0">
        <!-- Card Header -->
        <div class="card-header bg-primary text-white text-center py-3">
            <h2 class="mb-0">📋 All Projects</h2>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            @if ($projects->isEmpty())
                <!-- No Projects Alert -->
                <div class="alert alert-info text-center">
                    <strong>No projects found.</strong> Please check back later.
                </div>
            @else
                <!-- Responsive Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <!-- Table Header -->
                        <thead class="table-dark">
                            <tr>
                                <th>Title</th>
                                <th>Supervisor</th>
                                <th>Number of Students Required</th>
                                <th>Assigned Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        
                        <!-- Table Body -->
                        <tbody>
                            @foreach ($projects as $project)
                                <tr>
                                    <!-- Project Title -->
                                    <td class="fw-bold">{{ $project->title }}</td>

                                    <!-- Supervisor -->
                                    <td>{{ $project->supervisor->name }}</td>

                                    <!-- Students Required -->
                                    <td class="text-center">{{ $project->students_required }}</td>

                                    <!-- Assigned Students -->
                                    <td>
                                        @if ($project->students->isEmpty())
                                            <span class="text-muted">No students assigned</span>
                                        @else
                                            <ul class="list-unstyled mb-0">
                                                @foreach ($project->students as $student)
                                                    <li><i class="fa fa-user text-primary"></i> {{ $student->name }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="text-center">
                                        <a href="{{ route('students.projects.details', $project->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fa fa-eye"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>

        <!-- Card Footer -->
        <div class="card-footer text-muted text-center">
            Total Projects: {{ $projects->count() }}
        </div>
    </div>
</div>
@endsection
