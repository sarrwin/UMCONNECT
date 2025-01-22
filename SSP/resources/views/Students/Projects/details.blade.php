@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Project Details Card -->
    <div class="card shadow-lg border-0 rounded-3">
        <!-- Card Header -->
        <div class="card-header bg-primary text-white text-center fw-bold" style="font-size: 1.5rem;">
            📋 {{ $project->title }}
        </div>

        <!-- Card Body -->
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <tbody>
                        <tr>
                            <th class="bg-light fw-semibold" style="width: 30%;">📄 Description</th>
                            <td>{{ $project->description }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light fw-semibold">👨‍🏫 Supervisor</th>
                            <td>{{ $project->supervisor->name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light fw-semibold">👥 Number of Students Required</th>
                            <td>{{ $project->students_required }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light fw-semibold">🎓 Assigned Students</th>
                            <td>
                                @if ($project->students->isEmpty())
                                    <span class="text-muted">No students assigned yet.</span>
                                @else
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($project->students as $student)
                                            <li><i class="fa fa-user text-primary me-1"></i> {{ $student->name }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light fw-semibold">📅 Session</th>
                            <td>{{ $project->session }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light fw-semibold">🏢 Department</th>
                            <td>{{ $project->department }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light fw-semibold">🛠️ Tools</th>
                            <td>{{ $project->tools }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer text-center">
            <a href="{{ route('students.projects.index_all') }}" class="btn btn-outline-primary px-4 py-2 fw-bold">
                <i class="fa fa-arrow-left me-1"></i> Back to All Projects
            </a>
        </div>
    </div>
</div>

<!-- Additional Styling -->
<style>
    .table th {
        background-color: #f8f9fa !important;
        font-size: 1.1rem;
        padding: 12px;
    }

    .table td {
        font-size: 1rem;
        padding: 12px;
    }

    .card-header {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .card-footer {
        background-color: #f8f9fa;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .btn-outline-primary {
        transition: all 0.3s ease-in-out;
    }

    .btn-outline-primary:hover {
        background-color: #007bff;
        color: #fff;
    }
</style>
@endsection
