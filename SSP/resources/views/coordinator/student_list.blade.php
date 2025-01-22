@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Page Title -->
    <h1 class="text-center text-primary mb-4">👩‍🎓 Students in Your Department</h1>

    <!-- Table Section -->
    <div class="table-responsive shadow-lg p-4 bg-white rounded">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Matric No</th>
                    <th>Department</th>
            
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email }}</td>
                        <td>{{ $student->matric_number }}</td>
                        <td>
                        {{ $student->department }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No students found in your department.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .table {
        border: 1px solid #ddd;
        background-color: #f9f9f9;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }

    .table thead th {
        text-transform: uppercase;
        letter-spacing: 1px;
        text-align: center;
    }

    h1.text-primary {
        font-size: 2.5rem;
        font-weight: bold;
    }

    .shadow-lg {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06);
    }

    .bg-white {
        background-color: #fff;
    }

    .rounded {
        border-radius: 10px;
    }
</style>
@endsection
