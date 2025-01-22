@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="container mt-4">



    <!-- Button to trigger the create entry modal -->
    <div class="card shadow border-0">
        <div class="card-header bg-[#4C0865] text-Black">
            <h5 class="mb-0 text-center">Logbook Management</h5>
            <button class="btn btn-outline-primary" onclick="printLogbook()">
            <i class="fa fa-print"></i> Print Logbook
        </button>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Button to trigger the create entry modal -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createLogbookModal">
                <i class="fa fa-plus-circle"></i> Create New Entry
            </button>

            <!-- Filters -->
            <form method="GET" action="" class="mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="searchInput" class="form-label">
                            <i class="fa fa-search"></i> Search by Activity or Student
                        </label>
                        <input type="text" id="searchInput" name="search" class="form-control" placeholder="Search..." value="{{ $filters['search'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="filterVerified" class="form-label">
                            <i class="fa fa-check-circle"></i> Filter by Verified Status
                        </label>
                        <select id="filterVerified" name="verified" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ $filters['verified'] === '1' ? 'selected' : '' }}>Verified</option>
                            <option value="0" {{ $filters['verified'] === '0' ? 'selected' : '' }}>Not Verified</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ url()->current() }}" class="btn btn-secondary w-100">
                            <i class="fa fa-refresh"></i> Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fa fa-user"></i> Student</th>
                            <th><i class="fa fa-tasks"></i> Activity</th>
                            <th><i class="fa fa-calendar"></i> Date</th>
                            <th><i class="fa fa-check-circle"></i> Verified</th>
                            <th class="text-center"><i class="fa fa-cogs"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logbookEntries as $entry)
                            <tr>
                                <td>{{ optional($entry->student)->name ?? 'N/A' }}</td>
                           
                                <td>{{ $entry->activity }}</td>
                               
                                <td>{{ \Carbon\Carbon::parse($entry->activity_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $entry->verified ? 'bg-success' : 'bg-danger' }}">
                                        {{ $entry->verified ? 'Verified' : 'Not Verified' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if ($entry->logbookFiles)
                                            @foreach ($entry->logbookFiles as $file)
                                                <a href="{{ Storage::url($file->file_path) }}" class="btn btn-outline-info btn-sm" target="_blank" title="View File">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endforeach
                                        @endif

                                        @if ($entry->student_id === Auth::id() && !$entry->verified)
                                            <button class="btn btn-outline-warning btn-sm edit-logbook" 
                                            
                                            data-id="{{ $entry->id }}"
                data-activity="{{ $entry->activity }}"
                data-date="{{ $entry->activity_date }}"
                data-file-id="{{ $entry->logbookFiles->first()->id ?? '' }}"
                data-file-url="{{ $entry->logbookFiles->first() ? Storage::url($entry->logbookFiles->first()->file_path) : '' }}"
                data-file-name="{{ $entry->logbookFiles->first() ? basename($entry->logbookFiles->first()->file_path) : '' }}">
                <i class="fa fa-pencil"></i> Edit
          
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="showDeleteModal({{ $entry->id }})">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this logbook entry?
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Activity Modal -->
<div class="modal fade" id="createLogbookModal" tabindex="-1" aria-labelledby="createLogbookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createLogbookModalLabel">Create Logbook Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="logbookForm" action="{{ route('students.logbook.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="project_id" class="form-label">Project</label>
                        <select name="project_id" id="project_id" class="form-select" required>
                            <option value="" disabled selected>Select a project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                        </select>
                        <div id="projectError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="activity" class="form-label">Activity</label>
                        <textarea name="activity" id="activity" class="form-control" required></textarea>
                        <div id="activityError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="activity_date" class="form-label">Activity Date</label>
                        <input type="date" name="activity_date" id="activity_date" class="form-control" required>
                        <div id="activityDateError" class="text-danger"></div>
                    </div>
                    <div class="mb-3">
                        <label for="reference_file" class="form-label">Upload Reference Document</label>
                        <input type="file" name="reference_file" id="reference_file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                        <div id="fileError" class="text-danger"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Activity Modal -->
<div class="modal fade" id="editLogbookModal" tabindex="-1" aria-labelledby="editLogbookModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLogbookModalLabel">Edit Logbook Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editLogbookForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editActivity" class="form-label">Activity</label>
                        <textarea name="activity" id="editActivity" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editActivityDate" class="form-label">Activity Date</label>
                        <input type="date" name="activity_date" id="editActivityDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="editReferenceFile" class="form-label">Upload Reference Document</label>
                        <input type="file" name="reference_file" id="editReferenceFile" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                    </div>
                    <div id="currentFileContainer" class="mb-3 d-none">
    <label for="currentFile" class="form-label">Current File</label>
    <p id="currentFileLink">
        <a href="" target="_blank" id="currentFileAnchor">View Current File</a>
    </p>
    <div class="form-check">
        <input type="checkbox" name="remove_file" id="removeFile" class="form-check-input" value="1">
        <label for="removeFile" class="form-check-label">Remove current file</label>
    </div>
</div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>




<script>
    function showDeleteModal(entryId) {
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = `/logbooks/${entryId}`;
        deleteModal.show();
    }
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.edit-logbook').forEach(button => {
        button.addEventListener('click', function () {
            const entryId = this.getAttribute('data-id');
            const activity = this.getAttribute('data-activity');
            const activityDate = this.getAttribute('data-date');
            const fileUrl = this.getAttribute('data-file-url');
            const fileName = this.getAttribute('data-file-name');

            document.getElementById('editActivity').value = activity;
            document.getElementById('editActivityDate').value = activityDate;

            const form = document.getElementById('editLogbookForm');
            form.action = `/students/logbook/${entryId}`;

            const currentFileContainer = document.getElementById('currentFileContainer');
            const currentFileAnchor = document.getElementById('currentFileAnchor');
            if (fileUrl) {
                currentFileContainer.classList.remove('d-none');
                currentFileAnchor.href = fileUrl;
                currentFileAnchor.textContent = fileName || 'View Current File';
            } else {
                currentFileContainer.classList.add('d-none');
            }

            const editModal = new bootstrap.Modal(document.getElementById('editLogbookModal'));
            editModal.show();
        });
    });
});


document.getElementById('logbookForm').addEventListener('submit', function (e) {
        // Clear previous error messages
        document.getElementById('projectError').textContent = '';
        document.getElementById('activityError').textContent = '';
        document.getElementById('activityDateError').textContent = '';
        document.getElementById('fileError').textContent = '';

        let isValid = true;

        // Validate Project
        const projectId = document.getElementById('project_id').value;
        if (projectId === '') {
            document.getElementById('projectError').textContent = 'Please select a project.';
            isValid = false;
        }

        // Validate Activity
        const activity = document.getElementById('activity').value.trim();
        if (activity === '') {
            document.getElementById('activityError').textContent = 'Activity is required.';
            isValid = false;
        }

        // Validate Activity Date
        const activityDate = document.getElementById('activity_date').value.trim();
        if (activityDate === '') {
            document.getElementById('activityDateError').textContent = 'Activity date is required.';
            isValid = false;
        }

        // Validate Reference File (optional)
        const referenceFile = document.getElementById('reference_file').value;
        if (referenceFile !== '') {
            const allowedExtensions = /(\.pdf|\.doc|\.docx|\.xls|\.xlsx|\.png|\.jpg|\.jpeg)$/i;
            if (!allowedExtensions.test(referenceFile)) {
                document.getElementById('fileError').textContent = 'Invalid file format. Please upload a valid file.';
                isValid = false;
            }
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            e.preventDefault();
        }
    });



    document.addEventListener('DOMContentLoaded', function () {
    const filterInputs = ['searchInput', 'filterProject', 'filterVerified'];
    const resetFiltersButton = document.getElementById('resetFilters');

    function applyFilters() {
        const params = new URLSearchParams();

        const search = document.getElementById('searchInput').value;
        const projectId = document.getElementById('filterProject').value;
        const verified = document.getElementById('filterVerified').value;

        if (search) params.append('search', search);
        if (projectId) params.append('project_id', projectId);
        if (verified) params.append('verified', verified);

        window.location.href = `${window.location.pathname}?${params.toString()}`;
    }

    filterInputs.forEach(inputId => {
        const inputElement = document.getElementById(inputId);
        inputElement?.addEventListener('change', applyFilters);
    });

    resetFiltersButton?.addEventListener('click', function () {
        window.location.href = window.location.pathname;
    });
});


    function printLogbook() {
        window.location.href = "{{ route('logbook.pdf', ['project' => $project->id]) }}";
    }


</script>


<style>
    .card:hover {
    transform: none !important; /* Prevents moving up */
    box-shadow: none !important; /* Prevents shadow change */

    
}

.container {
    max-width: 80%; /* Adjusts the container to 95% of the viewport width */
    margin: 0 auto; /* Centers the container */
}


</style>
@endsection
