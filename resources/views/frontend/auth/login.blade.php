<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login - SMM Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/icon/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 420px;
            padding: 40px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(0);
            transition: all 0.3s ease;
        }
        .login-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
        }
        .login-box h4 {
            color: #1a1a1a;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .login-box p {
            color: #666;
            font-size: 15px;
            margin-bottom: 35px;
            line-height: 1.5;
        }
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        .form-group input {
            width: 100%;
            padding: 14px 20px;
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            background: rgba(255, 255, 255, 0.9);
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            background: #fff;
        }
        .form-group input::placeholder {
            color: #999;
            font-size: 14px;
        }
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 6px;
            border: 2px solid #e1e1e1;
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
            appearance: none;
            background: white;
        }
        .remember-me input[type="checkbox"]:checked {
            background: #667eea;
            border-color: #667eea;
        }
        .remember-me input[type="checkbox"]:checked:after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 14px;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .remember-me label {
            color: #666;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }
        .forgot-link {
            color: #667eea;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: 500;
        }
        .forgot-link:hover {
            color: #764ba2;
            text-decoration: none;
        }
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .submit-btn:after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
        .submit-btn:hover:after {
            left: 100%;
        }
        .signup-link {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .signup-link a:hover {
            color: #764ba2;
        }
        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 30px;
            font-size: 14px;
            line-height: 1.5;
        }
        .alert-danger {
            background-color: #fff5f5;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .alert-success {
            background-color: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        .alert ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        @media (max-width: 480px) {
            .login-box {
                padding: 30px 20px;
            }
            .login-box h4 {
                font-size: 24px;
            }
            .form-group input {
                padding: 12px 16px;
            }
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h4>Sign In</h4>
        <p>Enter your email and password to access the panel</p>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Email address" required autofocus>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
            <div class="remember-forgot">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
            </div>
            <button type="submit" class="submit-btn">Sign In</button>
        </form>
        <div class="signup-link">
            Don't have an account? <a href="{{ route('register') }}">Sign up</a>
        </div>
    </div>
</body>

</html> 