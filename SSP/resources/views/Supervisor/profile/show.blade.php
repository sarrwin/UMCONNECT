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
                        <img src="{{ asset('uploads/' . $supervisor->profile_picture) }}" alt="Profile Picture"
                             class="img-fluid rounded-circle mb-3 shadow-sm" 
                             style="width: 180px; height: 180px; object-fit: cover;">
                    </div>
                    <h4 class="fw-bold">{{ $supervisor->name }}</h4>
                    <p class="text-muted mb-1"><strong>Department:</strong> {{ $supervisor->department }}</p>
                    <p class="text-muted mb-1"><strong>Area of Expertise:</strong> {{ $supervisor->area_of_expertise }}</p>
                    <p class="text-muted"><strong>Contact:</strong> {{ $supervisor->contact_number }}</p>
                    <p class="text-muted"><strong>Email:</strong> <a href="mailto:{{ $supervisor->email }}">{{ $supervisor->email }}</a></p>
                    @auth
                        @if(auth()->user()->role !== 'admin')
                            <a href="{{ route('appointments.slots', $supervisor->id) }}" class="btn btn-primary w-100 mt-3">Book Appointment</a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Supervisor Details -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white fw-bold" style="background-color: #584f7a;">
                    Supervisor's Details
                </div>
                <div class="card-body">
                    <p><strong>Office Address:</strong> {{ $supervisor->office_address }}</p>
                    <p><strong>Area of Expertise:</strong> {{ $supervisor->area_of_expertise }}</p>
                    <p><strong>Department:</strong> {{ $supervisor->department }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Supervisor's Projects -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white fw-bold" style="background-color: #584f7a;">
                    Projects Supervised
                </div>
                <div class="card-body">
                    <!-- Filter by Session -->
                    <form method="GET" action="{{ route('supervisor.profile.show', $supervisor->id) }}" class="d-flex align-items-center mb-4">
                        <label for="session" class="form-label me-3 mb-0">Filter by Session:</label>
                        <select name="session" id="session" class="form-select me-3" style="width: 250px;">
                            <option value="">All Sessions</option>
                            @foreach($sessions as $session)
                                <option value="{{ $session }}" {{ request('session') == $session ? 'selected' : '' }}>
                                    {{ $session }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </form>

                    <!-- Projects Table -->
                    @if($projects->isEmpty())
                        <p class="text-muted text-center">No projects found for the selected session.</p>
                    @else
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>Project Title</th>
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
