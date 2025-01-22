<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UMConnect</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
        body {
            margin: 0;
            font-family: Figtree, sans-serif;
            display: flex;
            min-height: 100vh;
            background-color: #f9f9f9;
        }

        /* Left Image Section */
        .left-section {
            width: 50%;
            background: url('{{ asset("UMConnect_background.png") }}') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        /* Right Section */
        .right-section {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            text-align: center;
        }

        .right-section h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1.5rem;
        }

        /* Button Styling */
        .btn {
            display: inline-block;
            margin: 0.5rem;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
            border: 2px solid #3498db;
        }

        .btn-primary:hover {
            background-color: white;
            color: #3498db;
        }

        .btn-secondary {
            background-color: #FF2D20;
            color: white;
            border: 2px solid #FF2D20;
        }

        .btn-secondary:hover {
            background-color: white;
            color: #FF2D20;
        }

        footer {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #777;
        }
    </style>
</head>
<body>
    <!-- Left Section with Image -->
    <div class="left-section"></div>

    <!-- Right Section with Login/Register -->
    <div class="right-section">
        <h1>Welcome to UMConnect</h1>
        <p>Your ultimate supervision platform for students and supervisors.</p>

        <!-- Buttons -->
        @if (Route::has('login'))
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary">Log In</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
                @endif
            @endauth
        @endif

        <!-- Footer -->
        <footer>
            <!-- Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }}) -->
        </footer>
    </div>
</body>
</html>
