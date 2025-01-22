@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Page Title -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white text-center fw-bold">
            <h2><i class="fa fa-comments me-2"></i> Feedback Management</h2>
        </div>
    </div>

    <!-- Feedback Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead class="table-dark text-center">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Screenshot</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $feedback)
                            <tr class="text-center">
                                <!-- Index -->
                                <td class="fw-bold">{{ $loop->iteration }}</td>
                                
                                <!-- User -->
                                <td>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div>
                                            <i class="fa fa-user-circle text-primary me-1"></i> 
                                            <strong>
                @if($feedback->user->role === 'supervisor')
    <a href="{{ route('supervisor.profile.show', $feedback->user->id) }}" class="text-decoration-none text-primary">
        {{ $feedback->user->name }}
    </a>
@else
    <a href="{{ route('students.profile.show', $feedback->user->id) }}" class="text-decoration-none text-primary">
        {{ $feedback->user->name }}
    </a>
@endif
                </strong>
                                        </div>
                                    </div>
                                </td>

                                <!-- Subject -->
                                <td>{{ $feedback->subject }}</td>

                                <!-- Message -->
                                <td>
    <div title="{{ $feedback->message }}">
        {{ $feedback->message }}
    </div>
</td>


                                <!-- Screenshot -->
                                <td>
                                    @if($feedback->screenshot)
                                        <a href="{{ asset('storage/' . $feedback->screenshot) }}" target="_blank" 
                                           class="btn btn-sm btn-info">
                                            <i class="fa fa-image"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted">No Screenshot</span>
                                    @endif
                                </td>

                                <!-- Status -->
                                <td>
                                    <span class="badge bg-{{ $feedback->status === 'resolved' ? 'success' : 'warning' }} p-2">
                                        {{ ucfirst($feedback->status) }}
                                    </span>
                                </td>

                                <!-- Action -->
                                <td>
                                    @if($feedback->status === 'pending')
                                        <form action="{{ route('feedback.resolve', $feedback->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fa fa-check-circle me-1"></i> Resolve
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">Resolved</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted fw-bold py-4">
                                    <i class="fa fa-info-circle"></i> No feedback available.
                                </td>
                            </tr>
                        @endforelse
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
    }
    .card-header {
        font-size: 1.5rem;
    }
    .btn-sm {
        font-size: 0.9rem;
        padding: 6px 10px;
    }
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection
