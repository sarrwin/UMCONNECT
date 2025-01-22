@extends('layouts.app')

@section('content')
<div class="container my-4">
    <!-- Page Header -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="fw-bold mb-0"><i class="fas fa-calendar-alt me-2"></i> Manage Appointment Requests</h2>
        </div>
        <div class="card-body">
            <!-- Notifications -->
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Table -->
            @if($appointments->isEmpty())
                <div class="alert alert-info text-center">
                    <strong>No appointments to manage.</strong>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Student</th>
                                <th>Meeting Type</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($appointments as $appointment)
                                <tr class="appointment-row" data-status="{{ strtolower($appointment->status) }}">
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
                                    <!-- Student -->
                                    <td>{{ $appointment->student->name }}</td>
                                    <!-- Meeting Type -->
                                    <td>
                                        @if ($appointment->project_id)
                                            <span class="badge bg-info text-white">Project Meeting</span>
                                            <small>({{ $appointment->project->title }})</small>
                                        @else
                                            <span class="badge bg-secondary">General Meeting</span>
                                        @endif
                                    </td>
                                    <!-- Reason -->
                                    <td>{{ $appointment->request_reason ?? 'N/A' }}</td>
                                    <!-- Status -->
                                    <td>
                                        <span class="badge 
                                            @if ($appointment->status == 'pending') bg-warning 
                                            @elseif ($appointment->status == 'accepted') bg-success 
                                            @else bg-danger 
                                            @endif">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </td>
                                    <!-- Actions -->
                                    <td>
                                        <form action="{{ route('supervisor.appointments.updateStatus', $appointment->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            <!-- Status Radio -->
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="radio" name="status" value="accepted" id="accept-{{ $appointment->id }}" {{ $appointment->status == 'accepted' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="accept-{{ $appointment->id }}">Accept</label>
                                                </div>
                                                <div class="form-check me-2">
                                                    <input class="form-check-input" type="radio" name="status" value="declined" id="decline-{{ $appointment->id }}" {{ $appointment->status == 'declined' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="decline-{{ $appointment->id }}">Decline</label>
                                                </div>
                                            </div>
                                            <!-- Decline Reason -->
                                            <div id="decline-reason-{{ $appointment->id }}" class="mt-2 {{ $appointment->status == 'declined' ? '' : 'd-none' }}">
                                                <textarea name="decline_reason" class="form-control form-control-sm" placeholder="Reason for decline">{{ $appointment->decline_reason ?? '' }}</textarea>
                                            </div>
                                            <!-- Submit Button -->
                                            <button type="submit" class="btn btn-sm btn-primary mt-2">Save</button>
                                        </form>
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

<!-- JavaScript for Decline Reason -->
<script>
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const id = this.id.split('-')[1];
            const reasonDiv = document.getElementById(`decline-reason-${id}`);
            if (this.value === 'declined') {
                reasonDiv.classList.remove('d-none');
            } else {
                reasonDiv.classList.add('d-none');
            }
        });
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

    .form-check-label {
        font-size: 0.85rem;
    }

    .form-control {
        font-size: 0.85rem;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
</style>
@endsection
