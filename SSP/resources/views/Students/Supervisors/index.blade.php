@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center my-4">Supervisors</h1>

    <!-- Filter by Department -->
      

    <!-- Supervisors List -->
    <div class="card shadow p-4">
    <form method="GET" action="{{ route('supervisors.index') }}" class="mb-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text bg-primary text-white">
                    <i class="fas fa-filter"></i>
                </span>
                <select name="department" class="form-select" onchange="this.form.submit()">
                    <!-- Default 'Choose Department' option -->
                    <option value="" {{ empty($selectedDepartment) ? 'selected' : '' }}>Choose Department</option>

                    <!-- Dynamic department options -->
                    @foreach ($departments as $department)
                        <option value="{{ $department }}" {{ $selectedDepartment === $department ? 'selected' : '' }}>
                            {{ $department }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</form>

        <ul class="list-group">
            @forelse ($supervisors as $supervisor)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-user-tie text-primary"></i> {{ $supervisor->name }}
                        </h5>
                        <p class="mb-0">
                            <i class="fas fa-building text-secondary"></i> 
                            <strong>Department:</strong> {{ $supervisor->supervisor->department }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('appointments.slots', $supervisor->id) }}" class="btn btn-success btn-sm mx-1">
                            <i class="fas fa-calendar-check"></i> Book Appointment
                        </a>
                        <a href="{{ route('supervisor.profile.show', $supervisor->id) }}" class="btn btn-info btn-sm mx-1">
                            <i class="fas fa-id-card"></i> View Profile
                        </a>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-center">
                    <i class="fas fa-exclamation-circle text-danger"></i> No supervisors found in this department.
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
