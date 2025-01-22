@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h1 class="fw-bold">👤 User Management</h1>
        <p class="text-muted">Manage users, verify coordinators, and maintain roles efficiently.</p>
    </div>

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

    <!-- Filter Section -->
<div class="card mb-4 shadow-sm border-0">
   
    <div class="card-body">
        <form action="{{ route('admin.index') }}" method="GET" class="row gx-3 gy-2 align-items-center">
          <!-- Filter Section -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-primary text-white fw-bold">
        <i class="fa fa-filter me-2"></i> Filter Users
    </div>
    <div class="card-body">
        <form action="{{ route('admin.index') }}" method="GET">
            <div class="row align-items-end">
                <!-- Role Filter -->
                <div class="col-md-4">
                    <label for="role" class="form-label fw-semibold">Filter by Role:</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">All Roles</option>
                        <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="coordinator" {{ request('role') === 'coordinator' ? 'selected' : '' }}>Coordinator</option>
                    </select>
                </div>
                <div class="col-md-4">
        <label for="department" class="form-label fw-semibold">Filter by Department:</label>
        <select name="department" id="department" class="form-select">
            <option value="">All Departments</option>
            @foreach($departments as $dept)
                <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                    {{ ucfirst($dept) }}
                </option>
            @endforeach
        </select>
    </div>
                <!-- Apply Button -->
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-filter me-1"></i> Apply
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


    <!-- User Table -->
    <div class="card shadow-lg border-0">
        <div class="card-header bg-secondary text-white fw-bold">
            <i class="fa fa-users me-2"></i> Users List
        </div>
        <div class="card-body">
            <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle table-responsive">

                    <thead class="table-dark text-center">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>ID</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       @forelse($users as $user)
                            <tr>
                                <!-- Name -->
                                <td>
    <strong>
        @if(isset($user->role) && $user->role === 'supervisor')
            <a href="{{ route('supervisor.profile.show', $user->id) }}" class="text-decoration-none text-primary">
                {{ $user->name }}
            </a>
        @elseif(isset($user->role) && $user->role === 'student')
            <a href="{{ route('students.profile.show', $user->id) }}" class="text-decoration-none text-primary">
                {{ $user->name }}
            </a>
        @else
            {{ $user->name }} <!-- Fallback for unknown roles -->
        @endif
    </strong>
</td>

                                <!-- Email -->
                                <td>{{ $user->email }}</td>

                                <!-- ID -->
                                <td class="text-center">
                                    @if($user->role === 'supervisor')
                                        {{ $user->supervisor->staff_id ?? 'N/A' }}
                                    @else
                                        {{ optional($user->student)->matric_number ?? 'N/A' }}
                                    @endif
                                </td>

                                <!-- Role -->
                                <td class="text-center">
                                    <span class="badge bg-info text-white">{{ ucfirst($user->role) }}</span>
                                </td>
                                 <!-- Role -->
                                 <td class="text-center">
                                 @if($user->role === 'supervisor')
                                        {{ $user->supervisor->department ?? 'ADMIN' }}
                                    @else
                                        {{ optional($user->student)->department ?? 'ADMIN' }}
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="text-center">
                                    @if($user->role === 'supervisor' && $user->supervisor)
                                        @if(!$user->supervisor->is_coordinator)
                                            <!-- Verify as Coordinator -->
                                            <form action="{{ route('admin.verify.coordinator', $user->supervisor->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fa fa-check"></i> Verify as Coordinator
                                                </button>
                                            </form>
                                        @else
                                            <!-- Demote to Supervisor -->
                                            <span class="badge bg-success me-2">Verified</span>
                                            <form action="{{ route('admin.demote.coordinator', $user->supervisor->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="fa fa-arrow-down"></i> Demote
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="text-muted"></span>
                                    @endif

                                    <!-- Delete User -->
                                    <form action="{{ route('admin.delete.user', $user->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Styling -->
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .card {
        transition: none !important; /* Disable all transitions */
        box-shadow: none !important; /* Remove hoverable shadow */
        cursor: default !important; /* Default pointer, not clickable */
    }

    /* Remove any movement effect on hover */
    .card:hover {
        transform: none !important; /* Prevent upward movement or scaling */
    }

    /* Ensure cards stay in their place */
    .card {
        position: static !important; /* Ensure cards are static, not absolute or relative for moving */
    }

.table-responsive {
    overflow-x: auto; /* Ensures proper scrolling for wide tables on smaller screens */
}

.table td,
.table th {
    word-wrap: break-word; /* Prevents text from overflowing */
    white-space: nowrap; /* Ensures consistent spacing */
    text-align: center; /* Centers text for better alignment */
}

.table th {
    min-width: 100px; /* Sets a minimum width for table headers */
}

.table td:last-child {
    width: 300px; /* Allocates enough space for action buttons */
}
.container {
    max-width: 75%; /* Adjusts the container to 95% of the viewport width */
    margin: 0 auto; /* Centers the container */
}
    .card-header {
        font-size: 1.2rem;
    }

    .btn-sm {
        font-size: 0.875rem;
        padding: 5px 10px;
    }

    .badge {
        font-size: 0.9rem;
        padding: 6px 12px;
        border-radius: 12px;
    }

    .alert {
        font-size: 0.95rem;
    }
    
   
</style>
@endsection
