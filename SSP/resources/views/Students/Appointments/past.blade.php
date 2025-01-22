@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Page Header -->
    <div class="card shadow-lg border-0">
        <div class="card-header text-white text-center py-3" style="background-color: #584f7a;">
            <h2 class="mb-0 fw-bold">📅 Past Appointments</h2>
        </div>
        
        <!-- Card Body -->
        <div class="card-body">
            <!-- Status Filter -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <label for="statusFilter" class="fw-semibold me-2">Filter by Status:</label>
                    <select id="statusFilter" class="form-select d-inline-block w-auto">
                        <option value="all">All</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>

            <!-- Appointment Table -->
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped align-middle" id="appointmentsTable">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Supervisor</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($appointments as $appointment)
                            <tr class="text-center appointment-row" data-status="{{ strtolower($appointment->status) }}">
                                <!-- Date -->
                                <td>{{ \Carbon\Carbon::parse($appointment->date)->format('d M Y') }}</td>
                                
                                <!-- Time -->
                                <td>
                                    {{ \Carbon\Carbon::parse($appointment->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                </td>

                                <!-- Supervisor -->
                                <td>
                                    @if ($appointment->slot && $appointment->slot->supervisor)
                                        {{ $appointment->slot->supervisor->name }}
                                    @elseif ($appointment->supervisor)
                                        {{ $appointment->supervisor->name }}
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td>
                                    <span class="badge 
                                        @if ($appointment->status == 'completed') bg-success
                                        @elseif ($appointment->status == 'cancelled') bg-danger
                                        @else bg-warning
                                        @endif">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No past appointments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer text-center text-muted">
            Updated on {{ now()->format('d M Y') }}
        </div>
    </div>
</div>

<!-- JavaScript for Filtering -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusFilter = document.getElementById('statusFilter');
        const rows = document.querySelectorAll('.appointment-row');

        statusFilter.addEventListener('change', function () {
            const filterValue = this.value.toLowerCase();

            rows.forEach(row => {
                const rowStatus = row.getAttribute('data-status');
                if (filterValue === 'all' || rowStatus === filterValue) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>

<!-- Styling -->
<style>
    .card-header {
        font-size: 1.5rem;
        font-weight: bold;
        color: white;
    }

    .form-select {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 12px;
    }

    .table thead {
        font-size: 1rem;
    }

    .card-footer {
        font-size: 0.9rem;
    }
</style>
@endsection
