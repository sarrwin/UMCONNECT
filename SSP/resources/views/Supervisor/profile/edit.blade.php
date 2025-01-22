@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <!-- Supervisor Profile Card -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-lg border-0 text-center" style="height: 100%;">
                <div class="card-header text-white fw-bold" style="background-color: #584f7a;">
                    Profile
                </div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                    <div class="position-relative">
                        <img src="{{ $supervisor->profile_picture ? asset('uploads/' . $supervisor->profile_picture) : asset('profile-placeholder.png') }}"
                             alt="Profile Picture"
                             class="img-fluid rounded-circle mb-3 shadow-sm"
                             style="width: 180px; height: 180px; object-fit: cover;">
                    </div>
                    <h4 class="fw-bold">{{ $supervisor->name }}</h4>
                    <p class="text-muted mb-1"><strong>Email:</strong> {{ $supervisor->email }}</p>
                    <p class="text-muted mb-1"><strong>Contact:</strong> {{ $supervisor->contact_number ?? 'N/A' }}</p>
                    <p class="text-muted"><strong>Department:</strong> {{ $supervisor->department ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white fw-bold" style="background-color: #584f7a;">
                    Edit Profile
                </div>
                <div class="card-body">
                    <form action="{{ route('supervisor.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Profile Picture -->
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label fw-semibold">Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" class="form-control">
                        </div>

                        <!-- Contact Number -->
                        <div class="mb-3">
                            <label for="contact_number" class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact_number" id="contact_number" class="form-control" 
                                   value="{{ old('contact_number', $supervisor->contact_number) }}">
                        </div>

                        <!-- Office Address -->
                        <div class="mb-3">
                            <label for="office_address" class="form-label fw-semibold">Office Address</label>
                            <input type="text" name="office_address" id="office_address" class="form-control" 
                                   value="{{ old('office_address', $supervisor->office_address) }}">
                        </div>

                        <!-- Department -->
                        <div class="mb-3">
                            <label for="department" class="form-label fw-semibold">Department</label>
                            <select name="department" id="department" class="form-select">
                                <option value="" disabled>Select Department</option>
                                @foreach(['Artificial Intelligence', 'Software Engineering', 'Computer System and Network', 'Multimedia', 'Information System'] as $department)
                                    <option value="{{ $department }}" {{ old('department', $supervisor->department) == $department ? 'selected' : '' }}>
                                        {{ $department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                      
                        <!-- Save Button -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Section -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white fw-bold" style="background-color: #584f7a;">
                    Projects
                </div>
                <div class="card-body">
                    @if($projects->isEmpty())
                        <p class="text-muted text-center">No projects found.</p>
                    @else
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Students</th>
                                    <th>Session</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                    <tr>
                                        <td>{{ $project->title }}</td>
                                        <td>{{ $project->description }}</td>
                                        <td>
                                            @foreach($project->students as $student)
                                                <span class="badge bg-secondary">{{ $student->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $project->session }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 10px;
    }
    .card-header {
        font-size: 1.25rem;
    }
    .img-fluid {
        border: 5px solid #D5C4F3;
    }
</style>
@endsection
