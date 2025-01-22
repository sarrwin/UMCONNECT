@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-lg border-0">
        <!-- Card Header -->
        <div class="card-header bg-primary text-white text-center">
            <h2 class="mb-0">📋 All Projects</h2>
        </div>
        
        <!-- Card Body -->
        <div class="card-body">
            <!-- Table Wrapper -->
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-hover align-middle">
                    <!-- Table Header -->
                    <thead class="table-dark" style="position: sticky; top: 0; z-index: 1020;">
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
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($project->students as $student)
                                            <li>
                                                <i class="fa fa-user text-primary"></i> {{ $student->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>

                                <!-- Actions -->
                                <td>
                                    <a href="{{ route('projects.details', $project->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer text-muted text-center">
            Total Projects: {{ $projects->count() }}
        </div>
    </div>
</div>
@endsection
