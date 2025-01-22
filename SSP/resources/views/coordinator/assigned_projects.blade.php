@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Page Title -->
    <h1 class="text-center text-primary mb-4">📋 Overall Assigned Projects In Your Department</h1>
<!-- Success and Error Messages -->
@if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-times-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- Tabs Section -->
    <ul class="nav nav-tabs mb-4" id="projectTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="assigned-projects-tab" data-bs-toggle="tab" data-bs-target="#assigned-projects" type="button" role="tab" aria-controls="assigned-projects" aria-selected="true">
                Assigned Projects
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="unassigned-supervisors-tab" data-bs-toggle="tab" data-bs-target="#unassigned-supervisors" type="button" role="tab" aria-controls="unassigned-supervisors" aria-selected="false">
                Unassigned Supervisors
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="unassigned-students-tab" data-bs-toggle="tab" data-bs-target="#unassigned-students" type="button" role="tab" aria-controls="unassigned-students" aria-selected="false">
                Unassigned Students
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="projectTabsContent">
        <!-- Assigned Projects Table -->
        <div class="tab-pane fade show active" id="assigned-projects" role="tabpanel" aria-labelledby="assigned-projects-tab">
            <div class="table-responsive shadow-lg p-4 bg-white rounded">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr class="text-center">
                            <th>Supervisor</th>
                            <th>Students</th>
                            <th>Project Title</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>{{ $project->supervisor->name ?? 'No Supervisor' }}</td>
                                <td>
                                    @foreach ($project->students as $student)
                                        <span class="badge bg-info text-dark">{{ $student->name }}</span>@if (!$loop->last), @endif
                                    @endforeach
                                </td>
                                <td>{{ $project->title }}</td>

                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#projectDetailsModal-{{ $project->id }}">
                                        <i class="fa fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No projects found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Unassigned Supervisors Table -->
        <div class="tab-pane fade" id="unassigned-supervisors" role="tabpanel" aria-labelledby="unassigned-supervisors-tab">
            <div class="table-responsive shadow-lg p-4 bg-white rounded">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr class="text-center">
                            <th>Supervisor</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supervisorsWithoutProjects as $supervisor)
                            <tr>
                                <td>{{ $supervisor->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">No unassigned supervisors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Unassigned Students Table -->
        <div class="tab-pane fade" id="unassigned-students" role="tabpanel" aria-labelledby="unassigned-students-tab">
            <div class="table-responsive shadow-lg p-4 bg-white rounded">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark">
                        <tr class="text-center">
                            <th>Student</th>
        
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentsWithoutProjects as $student)
                            <tr>
                                <td>{{ $student->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">No unassigned students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- Modal Section -->
   <!-- Modal Section -->
@foreach($projects as $project)
    <div class="modal fade" id="projectDetailsModal-{{ $project->id }}" tabindex="-1" aria-labelledby="projectDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="projectDetailsLabel">Project Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Project Title:</strong> {{ $project->title }}</p>
                    <p><strong>Supervisor:</strong> {{ $project->supervisor->name ?? 'No Supervisor' }}</p>
                    <p><strong>Students:</strong>
                        @foreach ($project->students as $student)
                            <span class="badge bg-secondary">{{ $student->name }}</span>@if (!$loop->last), @endif
                        @endforeach
                    </p>
                    <p><strong>Department:</strong> {{ $project->department }}</p>

                    <!-- Appointment Records -->
                    <h5 class="mt-4">📅 Appointment Records</h5>
                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">
                        @php
                            $projectAppointments = $appointments->filter(function ($appointment) use ($project) {
                                return $appointment->student_id && $appointment->supervisor_id && in_array($appointment->student_id, $project->students->pluck('id')->toArray());
                            });
                        @endphp
                        @if ($projectAppointments->isEmpty())
                            <p class="text-muted">No appointments found.</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Appointment Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projectAppointments as $appointment)
                                        <tr>
                                            <td>{{ $appointment->student->name }}</td>
                                            <td>{{ $appointment->date }}</td>
                                            <td>{{ ucfirst($appointment->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <!-- Button to navigate to the logbook -->
                    <a href="{{ route('coordinator.logbook', $project->id) }}" class="btn btn-info mt-3">
                        <i class="fa fa-book"></i> View Logbook
                    </a>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- Remind Students and Supervisor Button -->
                    <form action="{{ route('coordinator.remind', $project->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fa fa-bell"></i> Remind Students and Supervisor
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

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

    .badge {
        font-size: 0.875rem;
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
@endsection
