@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Page Title -->
    <div class="text-center mb-4">
        <h1 class="text-primary">📊 Department Analytics</h1>
    </div>

    <!-- Row for Dashboard -->
    <div class="row">
        <!-- Chart Section -->
        <div class="col-lg-8">
            <div class="chart-container shadow-lg p-4 rounded bg-light">
                <h5 class="text-primary text-center mb-4">Students with Projects</h5>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="studentsWithProjectsChart"></canvas>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('coordinator.assigned_projects') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-chart-line"></i> View Details
                    </a>
                </div>
            </div>
        </div>

        <!-- Cards Section -->
        <div class="col-lg-4">
            <div class="dashboard-card text-center shadow-lg p-4 rounded bg-white mb-4">
                <h5 class="text-primary">👩‍🎓 Total Students</h5>
                <p class="dashboard-number text-dark">{{ $totalStudents }}</p>
                <a href="{{ route('coordinator.students') }}" class="btn btn-outline-primary btn-sm mt-3">
                    <i class="fa fa-users"></i> View
                </a>
            </div>

            <div class="dashboard-card text-center shadow-lg p-4 rounded bg-white mb-4">
                <h5 class="text-primary">👨‍🏫 Total Supervisors</h5>
                <p class="dashboard-number text-dark">{{ $totalSupervisors }}</p>
                <a href="{{ route('coordinator.supervisors') }}" class="btn btn-outline-primary btn-sm mt-3">
                    <i class="fa fa-user-tie"></i> View
                </a>
            </div>

            <div class="dashboard-card text-center shadow-lg p-4 rounded bg-white">
                <h5 class="text-primary">📁 Total Projects</h5>
                <p class="dashboard-number text-dark">{{ $totalProjects }}</p>
                <a href="{{ route('coordinator.total_project') }}" class="btn btn-outline-primary btn-sm mt-3">
                    <i class="fa fa-folder-open"></i> View
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data for students with projects chart
        const totalStudents = {{ $totalStudents }};
        const studentsWithProjects = {{ $studentsWithProjects }};

        // Students with Projects Doughnut Chart
        new Chart(document.getElementById('studentsWithProjectsChart'), {
            type: 'doughnut',
            data: {
                labels: ['Students with Projects', 'Students without Projects'],
                datasets: [{
                    data: [studentsWithProjects, totalStudents - studentsWithProjects],
                    backgroundColor: ['#4CAF50', '#E0E0E0'],
                    hoverBackgroundColor: ['#388E3C', '#BDBDBD']
                }]
            },
            options: {
                cutout: '60%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            color: '#333',
                            font: {
                                size: 14,
                                family: "'Roboto', sans-serif"
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>

<style>
    .chart-container {
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 15px;
    }

    .dashboard-card {
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 15px;
        transition: transform 0.3s;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    .dashboard-number {
        font-size: 3rem;
        font-weight: bold;
    }

    h1.text-primary {
        font-size: 2.5rem;
        font-weight: bold;
    }

    h5.text-primary {
        font-size: 1.25rem;
        font-weight: 600;
    }

    .btn-outline-primary {
        font-size: 0.9rem;
        font-weight: 500;
    }

    .btn-outline-primary i {
        margin-right: 5px;
    }
</style>
@endsection
