@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-center my-4">Edit Project</h1>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Warning Alert -->
    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Project Edit Form -->
    <div class="card shadow-sm p-4">
        <form action="{{ route('supervisor.projects.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div class="mb-4">
                <label for="title" class="form-label fw-bold">Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $project->title) }}" required>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="form-label fw-bold">Description</label>
                <textarea name="description" id="description" class="form-control" rows="4" required>{{ old('description', $project->description) }}</textarea>
            </div>

            <!-- Number of Students Required -->
            <div class="mb-4">
                <label for="students_required" class="form-label fw-bold">Number of Students Required</label>
                <input type="number" name="students_required" id="students_required" class="form-control" min="1" value="{{ old('students_required', $project->students_required) }}" required>
            </div>

            <!-- Filter and Assign Students -->
            <div class="mb-4">
                <label class="form-label fw-bold">Filter Students</label>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="studentSearch" class="form-control" placeholder="Search by name">
                    </div>
                    <div class="col-md-6">
                        <select id="departmentFilter" class="form-select">
                            <option value="">Filter by Department</option>
                            <option value="Artificial Intelligence">Artificial Intelligence</option>
                            <option value="Software Engineering">Software Engineering</option>
                            <option value="Computer System and Network">Computer System and Network</option>
                            <option value="Multimedia">Multimedia</option>
                            <option value="Information System">Information System</option>
                        </select>
                    </div>
                </div>

                <label class="form-label fw-bold">Assign Students</label>
                <div class="border p-3" style="max-height: 200px; overflow-y: scroll;">
                    @foreach ($students as $student)
                        <div class="form-check">
                            <input 
                                type="checkbox" 
                                name="students[]" 
                                value="{{ $student->id }}" 
                                id="student-{{ $student->id }}" 
                                class="form-check-input"
                                data-name="{{ strtolower($student->name) }}"
                                data-department="{{ $student->department }}"
                                {{ $project->students->contains($student->id) ? 'checked' : '' }}
                            >
                            <label for="student-{{ $student->id }}" class="form-check-label">
                                {{ $student->name }} 
                            </label>
                        </div>
                    @endforeach
                </div>
                <small class="text-muted">Scroll to view more students.</small>
            </div>

            <!-- Session -->
            <div class="mb-4">
                <label for="session" class="form-label fw-bold">Session</label>
                <select name="session" id="session" class="form-select" required>
                    <option value="">Select Session</option>
                    <option value="SEMESTER 1 2024/2025" {{ old('session') == 'SEMESTER 1 2024/2025' ? 'selected' : '' }}>SEMESTER 1 2024/2025</option>
                    <option value="SEMESTER 2 2024/2025" {{ old('session') == 'SEMESTER 2 2024/2025' ? 'selected' : '' }}>SEMESTER 2 2024/2025</option>
                    <option value="SEMESTER 1 2025/2026" {{ old('session') == 'SEMESTER 1 2025/2026' ? 'selected' : '' }}>SEMESTER 1 2025/2026</option>
                    <option value="SEMESTER 2 2025/2026" {{ old('session') == 'SEMESTER 2 2025/2026' ? 'selected' : '' }}>SEMESTER 2 2025/2026</option>
                </select>
            </div>

            <!-- Department -->
            <div class="mb-4">
                <label for="department" class="form-label fw-bold">Department</label>
                <select name="department" id="department" class="form-select" required>
                    <option value="">Select Department</option>
                    @foreach (['Artificial Intelligence', 'Software Engineering', 'Computer System and Network', 'Multimedia', 'Information System'] as $department)
                        <option value="{{ $department }}" {{ old('department', $project->department) == $department ? 'selected' : '' }}>
                            {{ $department }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tools -->
            <div class="mb-4">
                <label for="tools" class="form-label fw-bold">Tools</label>
                <input type="text" name="tools" id="tools" class="form-control" value="{{ old('tools', $project->tools) }}" placeholder="Enter tools, separated by commas" required>
            </div>

            <!-- Submit Button -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">Update Project</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const studentSearch = document.getElementById('studentSearch');
        const departmentFilter = document.getElementById('departmentFilter');
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="students[]"]');

        studentSearch.addEventListener('input', filterStudents);
        departmentFilter.addEventListener('change', filterStudents);

        function filterStudents() {
            const searchValue = studentSearch.value.toLowerCase();
            const departmentValue = departmentFilter.value;

            checkboxes.forEach(checkbox => {
                const studentName = checkbox.getAttribute('data-name');
                const studentDepartment = checkbox.getAttribute('data-department');
                const isChecked = checkbox.checked;
                const matchesName = studentName.includes(searchValue);
                const matchesDepartment = !departmentValue || studentDepartment === departmentValue;

                checkbox.parentElement.style.display = isChecked || (matchesName && matchesDepartment) ? '' : 'none';
         
            });
        }
    });
</script>
@endsection
