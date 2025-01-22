<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            color: #007bff;
            font-size: 24px;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Appointment Reminder</h1>
        <p>Dear {{ $user->name }},</p>
        <p>This is a reminder about your upcoming appointment:</p>
        <ul>
            <li><strong>Date:</strong> {{ $appointment->date }}</li>
            <li><strong>Time:</strong> {{ $appointment->time }}</li>
            <li><strong>With:</strong> {{ $appointment->supervisor->name ?? 'your supervisor' }}</li>
        </ul>
        <p>Please be prepared for the meeting at the scheduled time.</p>
        <p>Thank you,<br>Your Application Team</p>
    </div>
</body>
</html>
