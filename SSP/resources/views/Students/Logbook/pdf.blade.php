<!-- resources/views/supervisor/logbook/pdf.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Logbook for {{ $project->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Logbook for Project: {{ $project->title }}</h1>
    <h2>UMConnect</h2>
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Activity</th>
                <th>Date</th>
                <th>Verified</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filteredEntries as $entry)
                <tr>
                    <td>{{ $entry->student->name ?? 'N/A' }}</td>
                    <td>{{ $entry->activity }}</td>
                    <td>{{ \Carbon\Carbon::parse($entry->activity_date)->format('d M Y') }}</td>
                    <td>{{ $entry->verified ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
