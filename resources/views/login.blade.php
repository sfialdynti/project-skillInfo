<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/css/bootstrap.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>Login</title>
    <style>
        
        body {
            background-color: #ea824a;
            background-image: url('{{ asset('assets/img/background/bg2.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            backdrop-filter: blur(6px);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-container {
            display: flex;
            align-items: center;
            justify-content: center;
            /* background-color: rgba(255, 255, 255, 0.8); */
            /* background-color: #0d203a; */
            backdrop-filter: blur(10px);
            border-radius: 25px;
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            width: 80%;
            max-width: 1000px;
            padding: 20px;
            height: 80%;
        }

        .welcome-section {
            flex: 1;
            text-align: center;
            padding: 20px;
        }

        .welcome-section h1 {
            font-weight: 700;
            font-size: 26px;
            margin-bottom: 15px;
            /* color: #0d203a; */
            color: white;
        }

        .welcome-section p {
            font-size: 16px;
            /* color: #555; */
            color: white;
        }

        .login-section {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form {
            /* background-color: white; */
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 50px 50px;
            border-radius: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            min-height: 400px;
            background-color: rgba(255, 255, 255, 0.2);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: 700;
            /* color: #0d203a; */
            color: white;
        }

        input {
            background-color: transparent;
            border-radius: 10px;
            border: white solid 0.5px;
            padding: 12px 15px;
            margin: 8px 0;
            width: 100%;
            color: white
        }

        input::placeholder {
            color: white;
        }

        button {
            border-radius: 20px;
            border: white solid 1px;
            background-color: #2a1608;
            color: white;
            font-size: 15px;
            font-weight: 700;
            margin: 10px;
            padding: 12px 80px;
            letter-spacing: 1px;
            text-transform: capitalize;
            transition: 0.3s ease-in-out;
        }

        button:hover {
            letter-spacing: 3px;
        }

        button:active {
            transform: scale(0.95);
        }

        
    </style>
</head>
<body>
    <div class="main-container">
        <div class="welcome-section" style="text-align: center;">
            <img src="{{ asset('assets/img/logosmkypc.png') }}" alt="Logo Skill Info" style="width: 85px;">
            <h1>Welcome to<br> Skill Info</h1>
            <p>To keep connected with us please login with your personal info</p>
            <p></p>
        </div>
        <div class="login-section">
            <form action="/auth" method="POST">
            @if (session('statusLogin'))
                <div class="alert-danger p-2">
                    {{ session('statusLogin') }}
                </div>
             @endif
                @csrf
                <h1>Log In Here</h1>
                <div class="mb-2">
                    <label for="email" class="text-white">Email</label>
                    <input type="email" name="email" placeholder="Email Address" class="@error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email')
                        <div class=" invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="text-white">Password</label>
                    <input type="password" name="password" placeholder="Password" class="@error('password') is-invalid @enderror">
                    @error('password')
                        <div class=" invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit">Log In</button>
            </form>
        </div>
    </div>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
