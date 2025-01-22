@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Card Wrapper -->
    <div class="card shadow-lg border-0">
        <!-- Card Header -->
        <div class="card-header text-white text-center py-3" style="background-color: #584f7a;">
            <h2 class="mb-0">👩‍🏫 Projects & Students Under Supervision</h2>
        </div>

        <!-- Card Body -->
        <div class="card-body">
            <!-- Centered Navigation Tabs -->
            <ul class="nav nav-tabs justify-content-center" id="supervisorTabs" role="tablist" style="gap: 20px;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-4 py-2" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button" role="tab" aria-controls="projects" aria-selected="true">
                        Projects & Students
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-4 py-2" id="leaderboard-tab" data-bs-toggle="tab" data-bs-target="#leaderboard" type="button" role="tab" aria-controls="leaderboard" aria-selected="false">
                        Leaderboard
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-4" id="supervisorTabsContent">
                <!-- Projects & Students Tab -->
                <div class="tab-pane fade show active" id="projects" role="tabpanel" aria-labelledby="projects-tab">
                    <table class="table table-hover table-striped align-middle">
                        <thead class="table-dark text-center">
                            <tr>
                                <th>Title</th>
                                <th>Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $project)
                                <tr>
                                    <td class="fw-bold">{{ $project->title }}</td>
                                    <td>
                                        @foreach ($project->students as $student)
                                            <div><i class="fa fa-user text-primary"></i> {{ $student->name }}</div>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        @if ($project->students->isNotEmpty())
                                            <a href="{{ route('supervisor.students.projects.view_student_project', $project->students->first()->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-chart-line"></i> View Progress
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Leaderboard Tab -->
<div class="tab-pane fade" id="leaderboard" role="tabpanel" aria-labelledby="leaderboard-tab">
    <h3 class="mb-4 text-center text-white py-3 rounded" style="background: #584f7a;">
        🏆 <span style="color: #FFD700;">Supervisor's Project Leaderboard</span> (Points-Based) 🏆
    </h3>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>Rank</th>
                    <th>Medal</th>
                    <th>Project Title</th>
                    <th>Students</th>
                    <th>Points</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $leaderboard = $projects->map(function ($project) {
                        $totalTasks = $project->tasks->count();
                        $completedTasks = $project->tasks->where('status', 'completed')->count();
                        $overdueTasks = $project->tasks->where('status', 'overdue')->count();
                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                        $points = ($completedTasks * 10);
                        if ($progress === 100) $points += 50;
                        $points -= ($overdueTasks * 2);

                        return [
                            'title' => $project->title,
                            'students' => $project->students->pluck('name')->toArray(),
                            'progress' => $progress,
                            'points' => $points,
                        ];
                    })->sortByDesc('points')->values();
                @endphp

                @forelse ($leaderboard as $index => $project)
                    <tr class="text-center">
                        <!-- Rank -->
                        <td>
                            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $index + 1 }}</span>
                        </td>

                        <!-- Medals -->
                        <td>
                            @if ($index == 0)
                                <span style="font-size: 1.5rem;">🥇</span>
                            @elseif ($index == 1)
                                <span style="font-size: 1.5rem;">🥈</span>
                            @elseif ($index == 2)
                                <span style="font-size: 1.5rem;">🥉</span>
                            @else
                                <span style="font-size: 1.5rem;">🎖️</span>
                            @endif
                        </td>

                        <!-- Project Title -->
                        <td class="fw-bold">{{ $project['title'] }}</td>

                        <!-- Students -->
                        <td>
                            <ul class="list-unstyled mb-0">
                                @foreach ($project['students'] as $student)
                                    <li><i class="fa fa-user text-primary"></i> {{ $student }}</li>
                                @endforeach
                            </ul>
                        </td>

                        <!-- Points -->
                        <td>
                            @if ($project['points'] > 0)
                                <span class="badge bg-success rounded-pill px-3 py-2">
                                    {{ $project['points'] }} pts
                                </span>
                            @elseif ($project['points'] == 0)
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                    {{ $project['points'] }} pts
                                </span>
                            @else
                                <span class="badge bg-danger rounded-pill px-3 py-2">
                                    {{ $project['points'] }} pts
                                </span>
                            @endif
                        </td>

                        <!-- Progress -->
                        <td>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated 
                                    @if ($project['progress'] >= 75) bg-success
                                    @elseif ($project['progress'] >= 50) bg-info
                                    @elseif ($project['progress'] >= 25) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    role="progressbar" 
                                    style="width: {{ $project['progress'] }}%;" 
                                    aria-valuenow="{{ $project['progress'] }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    {{ $project['progress'] }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No projects available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer text-muted text-center">
          
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    /* Tabs Styling */
    .nav-tabs .nav-link {
        border-radius: 10px;
        font-size: 1.1rem;
        color: #007bff;
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #4C0865, #6E3C9E);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        color: white;
        border-color:rgb(5, 5, 5);
       
    }

    .progress-bar {
        transition: width 0.5s ease-in-out;
        font-size: 14px;
        font-weight: bold;
        color: white;
    }

    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endsection
