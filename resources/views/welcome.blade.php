<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Product Management</title>
    
    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <style>
        /*! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.css */
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
        }
        body {
            margin: 0;
            font-family: 'Nunito', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f3f4f6; /* Replace with your desired background color */
        }
        h1 {
            font-size: 2rem;
            font-weight: 600;
            text-align: center;
        }
        .link {
            text-decoration: underline;
            color: #3b82f6; /* Replace with your desired link color */
        }
    </style>
</head>
<body>
    <h1>Product Management</h1>
    <div>
        @if (Route::has('login'))
            <div style="position: absolute; top: 20px; right: 20px;">
                @auth
                    <a href="{{ url('/home') }}" class="link">Home</a>
                @else
                    <a href="{{ route('login') }}" class="link">Log in</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 link">Register</a>
                    @endif
                @endauth
            </div>
        @endif
    </div>
</body>
</html>
