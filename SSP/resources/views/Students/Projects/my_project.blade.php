@extends('layouts.app')

@section('content')
<div class="container">


@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($project)
<div class="card shadow-lg border-0 rounded">
    <!-- Card Header with Tabs -->
    <div class="card-header" style="background: #584f7a; border-radius: 10px 10px 0 0;">
    <ul class="nav  nav-tabs border-bottom-0 justify-content-center" id="studentTabs" role="tablist" style="gap: 15px;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-4 py-2" id="project-details-tab" data-bs-toggle="tab" data-bs-target="#project-details" type="button" role="tab" aria-controls="project-details" aria-selected="true">
                <i class="bi bi-journal-text me-1"></i> Project Details & Tasks
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-4 py-2" id="leaderboard-tab" data-bs-toggle="tab" data-bs-target="#leaderboard" type="button" role="tab" aria-controls="leaderboard" aria-selected="false">
                <i class="bi bi-trophy me-1"></i> Leaderboard
            </button>
        </li>
    </ul>
</div>
<div class="card-body" style="background-color: #f9f9f9; border-radius: 0 0 10px 10px;">
    <!-- Tabs Content -->
    <div class="tab-content mt-3 " id="studentTabsContent">
        <!-- Project Details & Tasks Tab -->
        <div class="tab-pane fade show active " id="project-details" role="tabpanel" aria-labelledby="project-details-tab">
            <!-- Inner Tabs -->
            <ul class="nav nav-tabs border-bottom-0 justify-content-center" id="projectDetailsTabs" role="tablist" style="gap: 15px;">
                <li class="nav-item">
                    <button class="nav-link active px-4 py-2" id="task-list-tab" data-bs-toggle="tab" data-bs-target="#task-list" type="button" role="tab" aria-controls="task-list" aria-selected="true">
                        <i class="bi bi-list-check me-1"></i> Task List
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link px-4 py-2" id="project-timeline-tab" data-bs-toggle="tab" data-bs-target="#project-timeline" type="button" role="tab" aria-controls="project-timeline" aria-selected="false">
                        <i class="bi bi-calendar-week me-1"></i> Project Timeline
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link px-4 py-2" id="file-submission-tab" data-bs-toggle="tab" data-bs-target="#file-submission" type="button" role="tab" aria-controls="file-submission" aria-selected="false">
                        <i class="bi bi-upload me-1"></i> File Submission
                    </button>
                </li>
            </ul>

<!-- Subtabs Content -->
<div class="tab-content mt-3" id="projectDetailsTabsContent">
<!-- Task List & Timeline -->
<div class="tab-pane fade show active" id="task-list" role="tabpanel" aria-labelledby="task-list-tab">
<!-- Task Completion Progress -->
<div class="card mb-3 shadow-lg">
    <div class="card-body bg-gradient-light rounded">
        <h5 class="text-center mb-3 fw-bold text-dark">📊 Task Completion Progress</h5>
        <div class="progress mb-2" style="height: 25px; border-radius: 10px; overflow: hidden;">
            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                role="progressbar" 
                style="width: 0%; transition: width 1s ease-in-out;" 
                id="taskProgressBar">
                0%
            </div>
        </div>
        <p id="progressText" class="text-center text-muted mb-0">Calculating progress...</p>
    </div>
</div>

<!-- Filter and Add Button -->
<div class="card shadow-sm mb-3">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
        <h5 class="mb-0">
            <i class="fa fa-tasks me-2"></i> Task List
        </h5>
        <div class="d-flex align-items-center">
            <i class="fa fa-filter me-2"></i>
            <select id="taskFilter" class="form-select form-select-sm" style="width: 200px;" onchange="filterTasks()">
                <option value="all">All</option>
                <option value="todo">To Do</option>
                <option value="in-progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="overdue">Overdue</option>
            </select>
        </div>
    </div>
    <div class="card-body p-2 text-end">
        <button class="btn btn-success btn-sm" id="openAddTaskModal">
            <i class="fa fa-plus-circle me-1"></i> Add Task
        </button>
    </div>
</div>

<!-- Scrollable Task List -->
<div class="card shadow-sm">
    <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
        @foreach ($project->tasks as $task)
            <div class="card mb-2 task-item task-{{ strtolower(str_replace(' ', '-', $task->status)) }}" 
                 data-id="{{ $task->id }}" style="margin-bottom: 8px;">
                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                    <!-- Task Details -->
                    <div>
                        <h6 class="mb-1 fw-bold">{{ $task->title }}</h6>
                        <p class="mb-0 text-muted small">
                            <strong>Status:</strong> 
                            <span class="badge 
                                @if($task->status == 'completed') bg-success
                                @elseif($task->status == 'in-progress') bg-warning
                                @elseif($task->status == 'overdue') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($task->status) }}
                            </span><br>
                            <strong>Due:</strong> {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->toFormattedDateString() : 'No due date' }}<br>
                            <strong>Assigned To:</strong>{{ $task->student ? $task->student->name : 'Unassigned' }}
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-1">
                        <button 
                            class="btn btn-warning btn-sm" 
                            onclick="openCustomModal({ 
                                id: '{{ $task->id }}', 
                                text: '{{ $task->title }}', 
                                start_date: '{{ $task->start_date }}', 
                                due_date: '{{ $task->due_date }}', 
                                status: '{{ $task->status }}' ,
                                 assigned_to: '{{ $task->student_id }}'
                            })">
                            <i class="fa fa-edit"></i> Edit
                        </button>
                        <button 
                            class="btn btn-danger btn-sm" 
                            onclick="deleteTask('{{ $task->id }}')">
                            <i class="fa fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
</div>

    

<!-- Task Completion Progress -->
<div class="tab-pane fade" id="project-timeline" role="tabpanel" aria-labelledby="project-timeline-tab"  >
<div id="gantt_here" style="width: 100%; height: 550px;"></div>


     </div>
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
</div>

<!-- File Submission -->
<div class="tab-pane fade" id="file-submission" role="tabpanel" aria-labelledby="file-submission-tab">
<!-- Submitted Files Section -->
<div class="submitted-files-section">
    <h5 class="mb-4">Submitted Files</h5>

    <!-- Add File Button -->
    <button id="showFileForm" class="btn btn-primary btn-sm mb-3">
        <i class="fa fa-upload"></i> Submit a New File
    </button>

    <!-- File Filters -->
    <div class="mb-3">
        <label for="fileFilter" class="form-label">Filter Files:</label>
        <select id="fileFilter" class="form-select">
            <option value="all">All</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="pending">Pending</option>
        </select>
    </div>

    <!-- Files Grid -->
    <div class="row" id="fileGrid">

    <!-- File Submission Form (Initially Hidden) -->
<div class="file-submission-section mt-4" id="fileSubmissionForm" style="display: none;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Submit a File</h5>
            <form action="{{ route('students.projects.submit_file', $project->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file_type" class="form-label">File Type</label>
                    <select name="file_type" id="file_type" class="form-select" required>
                        <option value="">Select Task</option>
                        @foreach ($project->tasks as $task)
                            <option value="{{ $task->title }}">{{ $task->title }}</option>
                        @endforeach
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3" id="other_file_type_wrapper" style="display: none;">
                    <label for="other_file_type" class="form-label">Specify File Type</label>
                    <input type="text" name="other_file_type" id="other_file_type" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="file" class="form-label">Upload File</label>
                    <input type="file" name="file" id="file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-upload"></i> Submit File
                </button>
            </form>
        </div>
    </div>
</div>
        @forelse ($project->files as $file)
            <div class="col-md-4 file-item mb-3 file-{{ strtolower($file->approval_status) }}">
                <div class="card shadow-sm" 
                     style="
                        background-color: 
                            @if ($file->approval_status === 'approved') #d4edda; 
                            @elseif ($file->approval_status === 'rejected') #f8d7da; 
                            @else #fff3cd; 
                            @endif;
                        border-color: 
                            @if ($file->approval_status === 'approved') #c3e6cb; 
                            @elseif ($file->approval_status === 'rejected') #f5c6cb; 
                            @else #ffeeba; 
                            @endif;">
                    <div class="card-body">
                        <h6 class="card-title">{{ ucfirst($file->file_type) }}</h6>
                        <p class="card-text mb-2">
                            <strong>Version:</strong> {{ $file->version }}<br>
                            <strong>Status:</strong> 
                            <span class="badge 
                                @if ($file->approval_status === 'approved') bg-success
                                @elseif ($file->approval_status === 'rejected') bg-danger
                                @else bg-warning
                                @endif">
                                {{ ucfirst($file->approval_status) }}
                            </span>
                            <p class="card-text"><strong>Comments:</strong> {{ $file->comment ?? 'No comment yet' }}</p>
                        </p>
                        <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fa fa-eye"></i> View
                        </a>
                        @if (auth()->user()->isStudent())
    <button type="button" class="btn btn-danger btn-sm" onclick="showDeleteFileModal('{{ route('students.projects.delete_file', $file->id) }}')">
        <i class="fa fa-trash"></i> Delete
    </button>
@endif

                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                <p>No files submitted yet.</p>
            </div>
        @endforelse
    </div>
</div>


</div>

</div>








<!-- Leaderboard Tab -->
<div class="tab-pane fade" id="leaderboard" role="tabpanel" aria-labelledby="leaderboard-tab">
<h3 class="mb-4 text-center text-white py-3 rounded"  style="background: #584f7a";>
🏆 <span style="color: #FFD700;">Project Leaderboard</span> (Points-Based) 🏆
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
            $leaderboard = \App\Models\Project::with('tasks', 'students')
                ->where('supervisor_id', $project->supervisor_id)
                ->get()
                ->map(function ($proj) {
                    $totalTasks = $proj->tasks->count();
                    $completedTasks = $proj->tasks->where('status', 'completed')->count();
                    $overdueTasks = $proj->tasks->where('status', 'overdue')->count();
                    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

                    $points = ($completedTasks * 10);
                    if ($progress === 100) {
                        $points += 50;
                    }
                    $points -= ($overdueTasks * 2);

                    return [
                        'title' => $proj->title,
                        'students' => $proj->students->pluck('name')->join(', '),
                        'progress' => $progress,
                        'points' => $points,
                    ];
                })
                ->sortByDesc('points')
                ->values();
        @endphp

        @forelse ($leaderboard as $index => $proj)
            <tr class="text-center">
                <!-- Rank -->
                <td>
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        {{ $index + 1 }}
                    </span>
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
                <td class="fw-bold">{{ $proj['title'] }}</td>

                <!-- Students -->
                <td>
                    <ul class="list-unstyled mb-0">
                        @foreach (explode(', ', $proj['students']) as $student)
                            <li><i class="fa fa-user"></i> {{ $student }}</li>
                        @endforeach
                    </ul>
                </td>

                <!-- Points -->
                <td>
    @if ($proj['points'] > 0)
        <span class="badge bg-success rounded-pill px-3 py-2">
            {{ $proj['points'] }} pts
        </span>
    @elseif ($proj['points'] == 0)
        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
            {{ $proj['points'] }} pts
        </span>
    @else
        <span class="badge bg-danger rounded-pill px-3 py-2">
            {{ $proj['points'] }} pts
        </span>
    @endif
</td>


                <!-- Progress -->
                <td>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar 
                            @if ($proj['progress'] >= 75) bg-success
                            @elseif ($proj['progress'] >= 50) bg-info
                            @elseif ($proj['progress'] >= 25) bg-warning
                            @else bg-danger
                            @endif"
                            role="progressbar"
                            style="width: {{ $proj['progress'] }}%;"
                            aria-valuenow="{{ $proj['progress'] }}"
                            aria-valuemin="0" aria-valuemax="100">
                            {{ $proj['progress'] }}%
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

@else
    <p class="text-center">No project assigned yet.</p>
@endif
</div>

<!-- Task Modal -->
<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Add New Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="taskForm" action="{{ route('project.add_task', ['projectId' => $project->id, 'userId' => auth()->user()->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title</label>
                        <input type="text" name="title" id="title" class="form-control">
                        <div id="titleError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control">
                        <div id="startDateError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" id="due_date" class="form-control">
                        <div id="dueDateError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assign To</label>
                        <select name="assigned_to" id="assigned_to" class="form-select">
                            <option value="" disabled selected>Select a student</option>
                            @foreach ($project->students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endforeach
                        </select>
                        <div id="assignedToError" class="text-danger"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </div>
            </form>
        </div>
    </div>
</div>
    <div class="modal fade" id="customTaskModal" tabindex="-1" aria-labelledby="customTaskModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <form id="customTaskForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="customTaskModalLabel">Update Task Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Task:</strong> <span id="customTaskTitle"></span></p>
                <input type="hidden" id="customTaskId" name="taskId">
                <div class="mb-3">
                <label for="customTaskTitle" class="form-label">Task Title</label>
                <input type="text" id="customTaskTitleInput" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="customTaskStartDate" class="form-label">Start Date</label>
                <input type="date" id="customTaskStartDate" name="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="customTaskDueDate" class="form-label">Due Date</label>
                <input type="date" id="customTaskDueDate" name="due_date" class="form-control" required>
            </div>

                <div class="mb-3">
                    <label for="customTaskStatus" class="form-label">Status</label>
                    <select id="customTaskStatus" name="status" class="form-control" required>
                        <option value="todo">To Do</option>
                        <option value="in progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="mb-3">
                        <label for="custom_assigned_to" class="form-label">Assign To</label>
                        <select id="custom_assigned_to"id='assigned_to' name="assigned_to" class="form-control" required>
                            @foreach ($project->students as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} </option>
                            @endforeach
                        </select>
                      
               
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteFileModal" tabindex="-1" aria-labelledby="deleteFileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteFileModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this file? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <form id="deleteFileForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    document.getElementById('file_type').addEventListener('change', function () {
        const otherFileTypeWrapper = document.getElementById('other_file_type_wrapper');
        const otherFileTypeInput = document.getElementById('other_file_type');

        if (this.value === 'other') {
            otherFileTypeWrapper.style.display = 'block';
            otherFileTypeInput.required = true;
        } else {
            otherFileTypeWrapper.style.display = 'none';
            otherFileTypeInput.required = false;
        }
    });
    function showDeleteFileModal(actionUrl) {
    const deleteFileModal = new bootstrap.Modal(document.getElementById('deleteFileModal'));
    const deleteFileForm = document.getElementById('deleteFileForm');
    deleteFileForm.action = actionUrl;
    deleteFileModal.show();
}


    document.addEventListener('DOMContentLoaded', function () {
    const fileGrid = document.getElementById('fileGrid');
    const fileSubmissionForm = document.getElementById('fileSubmissionForm');
    const showFileFormButton = document.getElementById('showFileForm');
    const fileFilter = document.getElementById('fileFilter');
    const files = document.querySelectorAll('.file-item');

    // Show file submission form on button click
    showFileFormButton.addEventListener('click', function () {
        fileSubmissionForm.style.display = fileSubmissionForm.style.display === 'none' ? 'block' : 'none';
    });

    // Filter files based on status
    fileFilter.addEventListener('change', function () {
        const filterValue = this.value.toLowerCase();

        files.forEach(file => {
            if (filterValue === 'all' || file.classList.contains(`file-${filterValue}`)) {
                file.style.display = 'block';
            } else {
                file.style.display = 'none';
            }
        });
    });
});

</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        gantt.config.date_format = "%Y-%m-%d";
        gantt.config.columns = [
            { name: "text", label: "Task name", width: "*", tree: true },
            { name: "start_date", label: "Start Date", align: "center" },
            { name: "duration", label: "Duration", align: "center" },
            {
                name: "delete", label: "", width: 40, align: "center", template: function (task) {
                return `
                    <i class="fa fa-trash text-danger delete-task" 
                       style="cursor: pointer;" 
                       data-id="${task.id}" 
                       title="Delete Task">
                    </i>`;
            }
            }
        ];

        gantt.config.lightbox.sections = [
            { name: "description", height: 38, map_to: "text", type: "textarea", focus: true },
            { name: "status", height: 38, map_to: "status", type: "select", options: [
                { key: "todo", label: "To Do" },
                { key: "in progress", label: "In Progress" },
                { key: "completed", label: "Completed" }
            ]},
            { name: "time", type: "duration", map_to: "auto" }
        ];

        gantt.templates.task_class = function (start, end, task) {
            const today = new Date();
            const dueDate = gantt.date.parseDate(task.due_date, "xml_date");

            if (task.status === 'overdue') {
                return "overdue";
            } else if (task.status === 'in progress') {
                return "in-progress";
            } else if (task.status === 'completed') {
                return "completed";
            } else {
                return "todo";
            }
        };

        // Calculate Progress
        function calculateProgress() {
            const tasks = gantt.getTaskByTime();
            const totalTasks = tasks.length;
            const completedTasks = tasks.filter(task => task.status === 'completed').length;
            const progressPercentage = totalTasks > 0 ? (completedTasks / totalTasks) * 100 : 0;

            document.querySelector('.progress-bar').style.width = `${progressPercentage}%`;
            document.querySelector('.progress-bar').setAttribute('aria-valuenow', progressPercentage);
            document.querySelector('.progress-bar').textContent = `${Math.round(progressPercentage)}%`;

            const remainingTasks = totalTasks - completedTasks;
            document.getElementById('progressText').textContent = `Progress: ${Math.round(progressPercentage)}% - ${remainingTasks} task(s) left to complete 100%`;
        }

        // Load Tasks
        function loadTasks() {
            fetch(`/projects/{{ $project->id }}/tasks`)
                .then(response => response.json())
                .then(data => {
                    const tasks = data.data.map(task => ({
                        id: task.id,
                        text: task.title || task.text,
                        start_date: task.start_date,
                        duration: task.duration,
                        status: task.status || 'todo',
                        due_date: task.due_date
                    }));

                    gantt.clearAll();
                    gantt.parse({ data: tasks });
                    calculateProgress();
                })
                .catch(error => console.error("Error loading tasks:", error));
        }

        // Delete Task Functionality
     
        // Make deleteTask globally accessible
        window.deleteTask = function (taskId) {
    console.log("Attempting to delete task ID:", taskId);

    if (!confirm("Are you sure you want to delete this task?")) {
        return;
    }

    fetch(`/tasks/${taskId}`, {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
    })
        .then(response => response.json())
        .then(data => {
            console.log("Delete response:", data);
            if (data.success) {
                // Remove the task card from the DOM
                const taskElement = document.querySelector(`.task-item[data-id='${taskId}']`);
                if (taskElement) {
                    taskElement.remove();
                    location.reload();
                    console.log(`Task ID: ${taskId} removed from DOM.`);
                } else {
                    console.warn(`Task element with ID: ${taskId} not found in DOM.`);
                }

                alert("Task deleted successfully!");
            } else {
                alert("Failed to delete task. Please try again.");
            }
        })
        .catch(error => {
            console.error("Error deleting task:", error);
            alert("An error occurred while deleting the task. Please try again.");
        });
};


// Delegate event handling for delete-task icons
document.addEventListener("click", function (event) {
    if (event.target.classList.contains("delete-task")) {
        event.stopPropagation(); // Prevent triggering the onTaskClick event
        const taskId = event.target.getAttribute("data-id");
        deleteTask(taskId);
    }
});


gantt.attachEvent("onGanttRender", function () {
            document.querySelectorAll(".delete-task").forEach(icon => {
                icon.addEventListener("click", function () {
                    const taskId = this.getAttribute("data-id");
                    deleteTask(taskId);
                });
            });
        });


        // Add Task Modal
        document.getElementById('openAddTaskModal').addEventListener('click', () => {
            $('#taskModal').modal('show');
        });

        document.querySelector('#taskForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const form = event.target;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: new FormData(form)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        $('#taskModal').modal('hide');
                        form.reset();
                        loadTasks();
                        location.reload();
                    }
                })
                .catch(error => {
                    alert(`An error occurred: ${error.message}`);
                });
        });

        // Custom Task Modal
        gantt.attachEvent("onTaskClick", function (id, e) {
    const target = e.target;

    // Prevent opening the edit modal if the delete button is clicked
    if (target.classList.contains("delete-task")) {
        return false;
    }

    const task = gantt.getTask(id);
    openCustomModal(task);
    return false; // Prevent the default Gantt behavior if any
});

window.openCustomModal = function (task) {
    // Parse start_date and due_date
    const startDate = new Date(task.start_date);
    const dueDate = new Date(task.due_date);

    // Ensure valid date handling
    if (isNaN(startDate.getTime()) || isNaN(dueDate.getTime())) {
        console.error("Invalid date format:", task.start_date, task.due_date);
        alert("Invalid date format for the task.");
        return;
    }

    // Adjust dates to compensate for timezone offset
    const adjustedStartDate = new Date(startDate.getTime() + Math.abs(startDate.getTimezoneOffset() * 60000));
    const adjustedDueDate = new Date(dueDate.getTime() + Math.abs(dueDate.getTimezoneOffset() * 60000));

    // Set modal fields
    document.getElementById('customTaskTitle').innerText = task.text;
    document.getElementById('customTaskId').value = task.id;
    document.getElementById('customTaskTitleInput').value = task.text;
    if (task.assigned_to) {
        document.getElementById('custom_assigned_to').value = task.assigned_to;
    }
    // Format adjusted dates for input fields
    document.getElementById('customTaskStartDate').value = adjustedStartDate.toISOString().split("T")[0];
    document.getElementById('customTaskDueDate').value = adjustedDueDate.toISOString().split("T")[0];

    document.getElementById('customTaskStatus').value = task.status || 'todo';

    // Show the modal
    $('#customTaskModal').modal('show');
};


        document.getElementById('customTaskForm').addEventListener('submit', function (event) {
            event.preventDefault();

            const taskId = document.getElementById('customTaskId').value;
            const newTitle = document.getElementById('customTaskTitleInput').value;
            const newStartDate = document.getElementById('customTaskStartDate').value;
            const newDueDate = document.getElementById('customTaskDueDate').value;
            const newStatus = document.getElementById('customTaskStatus').value;
            const newAssigned = document.getElementById('custom_assigned_to').value;

            const task = gantt.getTask(taskId);
            if (newTitle) task.text = newTitle;
            if (newStartDate) task.start_date = gantt.date.str_to_date("%Y-%m-%d")(newStartDate);
            if (newDueDate) task.end_date = gantt.date.str_to_date("%Y-%m-%d")(newDueDate);
            task.status = newStatus;
            task.assigned_to=newAssigned;
            gantt.updateTask(taskId);
            sendTaskUpdate(taskId, { title: newTitle, start_date: newStartDate, due_date: newDueDate, status: newStatus,assigned_to:newAssigned });

            $('#customTaskModal').modal('hide');
            calculateProgress();
        });

        function sendTaskUpdate(id, data) {
            fetch(`/task/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data),
            })
                .then(response => response.json())
                
                .then(() => loadTasks())
                .then(() => {
            location.reload(); // Reload the page after a successful update
        })
                .catch(error => console.error("Error updating task:", error));
        }

        gantt.init("gantt_here");
        loadTasks();
      
    });
</script>

<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const taskFilter = document.getElementById('taskFilter');
    const tasks = document.querySelectorAll('.task-item');

    // Add event listener for filter dropdown
    taskFilter.addEventListener('change', function () {
        const filterValue = this.value.toLowerCase();
        console.log('Selected Filter:', filterValue);

        tasks.forEach(task => {
            // Check if the task matches the filter value or if "all" is selected
            if (filterValue === 'all' || task.classList.contains(`task-${filterValue}`)) {
                task.style.display = ''; // Show matching tasks
            } 
            
            else {
                task.style.display = 'none'; // Hide non-matching tasks
            }
        });
    });
});

document.getElementById('taskForm').addEventListener('submit', function (e) {
        // Clear previous error messages
        document.getElementById('titleError').textContent = '';
        document.getElementById('startDateError').textContent = '';
        document.getElementById('dueDateError').textContent = '';
        document.getElementById('assignedToError').textContent = '';

        let isValid = true;

        // Validate Task Title
        const title = document.getElementById('title').value.trim();
        if (title === '') {
            document.getElementById('titleError').textContent = 'Task Title is required.';
            isValid = false;
        }

        // Validate Start Date
        const startDate = document.getElementById('start_date').value.trim();
        if (startDate === '') {
            document.getElementById('startDateError').textContent = 'Start Date is required.';
            isValid = false;
        }

        // Validate Due Date
        const dueDate = document.getElementById('due_date').value.trim();
        if (dueDate === '') {
            document.getElementById('dueDateError').textContent = 'Due Date is required.';
            isValid = false;
        }

        // Validate Assigned To
        const assignedTo = document.getElementById('assigned_to').value;
        if (assignedTo === '') {
            document.getElementById('assignedToError').textContent = 'Please select a student.';
            isValid = false;
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            e.preventDefault();
        }
    });


</script>
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<style>
  .gantt_task_line.todo {
        background-color: #007bff !important; /* Blue for To Do */
    }
    .gantt_task_line.in-progress {
        background-color: #ffa500 !important; /* Orange for In Progress */
    }
    .gantt_task_line.completed {
        background-color: #28a745 !important; /* Green for Completed */
    }
    .gantt_task_line.overdue {
        background-color: #ff4c4c !important; /* Red for Overdue */
    }

    /* Task bar colors based on progress */
.gantt_completed .gantt_task_progress {
    background: #4CAF50 !important; /* Green for completed */
}

.gantt_in_progress .gantt_task_progress {
    background: #FF9800 !important; /* Orange for in progress */
}

.gantt_todo .gantt_task_progress {
    background: #2196F3 !important; /* Blue for to-do */
}

/* Customize the overall Gantt chart style */
.gantt_task_line {
    border-radius: 5px; /* Rounded corners for tasks */
    box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.2); /* Task shadow */
}

.gantt_task_progress {
    border-radius: 5px; /* Progress bar rounded corners */
}

/* Adjust scale header */
.gantt_scale_line {
    background-color: #f5f5f5;
    font-weight: bold;
}

</style>


<style>
    /* Remove bottom margin from Task Completion Progress */
   /* Task card styles */
.task-card-container {
    padding: 0.5rem;
    background-color: #f8f9fa; /* Light background for task container */
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

    .card:hover {
    transform: none !important; /* Prevents moving up */
    box-shadow: none !important; /* Prevents shadow change */
}

.task-item {
    border: 1px solid #ddd; /* Light border for cards */
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Subtle shadow */
}

.task-item .card-title {
    font-size: 1rem; /* Adjust title font size */
    font-weight: bold;
}

.task-item .card-text {
    font-size: 0.875rem; /* Smaller text for details */
    color: #6c757d; /* Muted text color */
}

/* Scrollbar customization (optional) */
.task-card-container::-webkit-scrollbar {
    width: 6px;
}

.task-card-container::-webkit-scrollbar-thumb {
    background-color: #007bff;
    border-radius: 3px;
}

.task-completed {
    background-color: #d4edda; /* Light green */
    border-color: #c3e6cb;    /* Green */
}

.task-in-progress {
    background-color: #fff3cd; /* Light yellow */
    border-color: #ffeeba;    /* Yellow */
}

.task-overdue {
    background-color: #f8d7da; /* Light red */
    border-color: #f5c6cb;    /* Red */
}

.task-todo {
    background-color: #d1ecf1; /* Light blue */
    border-color: #bee5eb;    /* Blue */
}

    .task-completion-section2 {
        margin-left: -10px !important; 
        width: 890px !important;
        height: 200px !important;
    }
    /* Reduce top margin for Project Timeline */
    .project-timeline-section {
        margin-top: -400px !important; /* Adjust this value to your liking */
        padding-top: 0 !important;
    }

    .file-submission-section {
        margin-left: -8px !important; 
        width:900px !important;/* Adjust this value to your liking */
        margin-top: 230px;
        padding-top: 0 !important;
        height:400px !important;
    }

    .project-section {
       
        width: 400px !important;/* Adjust this value to your liking */
        
        padding-top: 0 !important;
    }


    .progress-bar {
    transition: width 0.5s ease;
    font-size: 14px;
    font-weight: bold;
    color: white;
}

.progress-bar[aria-valuenow="100"] {
    background-color: #28a745; /* Dark Green for 100% */
}

.table td, .table th {
    vertical-align: middle;
}

h3.text-center {
    color: #6c757d; /* Muted color for title */
    font-weight: bold;
}

/* Tab Navigation Styling */
.nav-tabs .nav-link {
        color: #555;
        font-weight: 600;
        border: none;
        background-color: #f0f0f0;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active,
    .nav-tabs .nav-link:hover {
        color: #fff;
        background: linear-gradient(135deg, #4C0865, #6E3C9E);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Inner Tabs */
    #projectDetailsTabs .nav-link {
        border: none;
        color: #555;
        font-weight: 500;
        background-color: #e9e9e9;
        transition: background-color 0.3s ease;
    }

    #projectDetailsTabs .nav-link.active,
    #projectDetailsTabs .nav-link:hover {
        color: #fff;
        background: #6E3C9E;
    }

    /* Icons Styling */
    .nav-link i {
        font-size: 1rem;
    }
</style>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
