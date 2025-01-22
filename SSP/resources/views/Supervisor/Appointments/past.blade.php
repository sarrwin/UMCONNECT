@extends('layouts.app')

@section('content')
<div class="container my-4">
    <!-- Past Appointments Card -->
    <div class="card shadow-lg border-0">
        <!-- Card Header -->
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #584f7a;">
            <h5 class="mb-0">
                <i class="fas fa-calendar-check me-2"></i> Past Appointments
            </h5>
            <div class="d-flex align-items-center gap-3">
                <!-- Status Filter -->
                <div class="d-flex align-items-center">
                    <i class="fas fa-filter me-2"></i>
                    <select id="statusFilter" class="form-select w-auto bg-light text-dark" style="border-radius: 8px; font-size: 0.9rem;">
                        <option value="all" selected>All Status</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>

                <!-- Project Filter -->
                <div class="d-flex align-items-center">
                    <i class="fas fa-project-diagram me-2"></i>
                    <select id="projectFilter" class="form-select w-auto bg-light text-dark" style="border-radius: 8px; font-size: 0.9rem;">
                        <option value="all" selected>All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ strtolower($project->title) }}">{{ $project->title }}</option>
                        @endforeach
                        <option value="general">General</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-4">
            @if($appointments->isEmpty())
                <div class="alert alert-info text-center">
                    <strong>No past appointments found.</strong>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="appointmentsTable">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Booked By</th>
                                <th>Project</th>
                                <th>Reason</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr class="text-center appointment-row" 
                                    data-status="{{ strtolower($appointment->status) }}" 
                                    data-project="{{ strtolower($appointment->project->title ?? 'general') }}">
                                    <!-- Date -->
                                    <td>
                                        @if ($appointment->slot)
                                            {{ \Carbon\Carbon::parse($appointment->slot->date)->format('d M Y') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}
                                        @endif
                                    </td>
                                    
                                    <!-- Time -->
                                    <td>
                                        @if ($appointment->slot)
                                            {{ \Carbon\Carbon::parse($appointment->slot->start_time)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($appointment->slot->end_time)->format('h:i A') }}
                                        @else
                                            {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                        @endif
                                    </td>

                                    <!-- Booked By -->
                                    <td>
                                        @if ($appointment->student)
                                            <span class="fw-bold">{{ $appointment->student->name }}</span>
                                        @else
                                            <span class="text-muted">No Student</span>
                                        @endif
                                    </td>

                                    <!-- Project -->
                                    <td>{{ $appointment->project->title ?? 'General' }}</td>

                                    <!-- Reason -->
                                    <td>
                                        @if ($appointment->request_reason)
                                            {{ $appointment->request_reason }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td>
                                        <span class="badge 
                                            @if ($appointment->status === 'completed') bg-success
                                            @elseif ($appointment->status === 'cancelled') bg-danger
                                            @else bg-warning
                                            @endif">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- JavaScript for Filters -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusFilter = document.getElementById('statusFilter');
        const projectFilter = document.getElementById('projectFilter');
        const rows = document.querySelectorAll('.appointment-row');

        function applyFilters() {
            const statusValue = statusFilter.value.toLowerCase();
            const projectValue = projectFilter.value.toLowerCase();

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                const rowProject = row.getAttribute('data-project');

                const statusMatch = statusValue === 'all' || rowStatus === statusValue;
                const projectMatch = projectValue === 'all' || rowProject === projectValue;

                if (statusMatch && projectMatch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        statusFilter.addEventListener('change', applyFilters);
        projectFilter.addEventListener('change', applyFilters);
    });
</script>

<!-- Styles -->
<style>
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
        font-size: 0.9rem;
        padding: 8px 12px;
        border-radius: 12px;
    }

    #statusFilter, #projectFilter {
        font-size: 0.9rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header .form-select {
        border: none;
        box-shadow: none;
    }
</style>
@endsection
