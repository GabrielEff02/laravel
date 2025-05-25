<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body,
        html {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background particles */
        .bg-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 80px;
            height: 80px;
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 60px;
            height: 60px;
            left: 20%;
            animation-delay: 1s;
        }

        .particle:nth-child(3) {
            width: 40px;
            height: 40px;
            left: 70%;
            animation-delay: 2s;
        }

        .particle:nth-child(4) {
            width: 100px;
            height: 100px;
            left: 80%;
            animation-delay: 3s;
        }

        .particle:nth-child(5) {
            width: 50px;
            height: 50px;
            left: 60%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.3;
            }

            50% {
                transform: translateY(-100px) rotate(180deg);
                opacity: 0.8;
            }
        }

        /* Container layout */
        .container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .row {
            width: 100%;
            max-width: 1200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .col-lg-6 {
            width: 100%;
            max-width: 500px;
            position: relative;
        }

        /* Background decorative element */
        .bg2 {
            display: none;
            /* Hide original background image */
        }

        /* Modern card container */
        .centered {
            position: relative;
            width: 100%;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 3rem;
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(255, 255, 255, 0.05);
            animation: slideIn 0.8s ease-out;
            transform: none;
            top: auto;
            left: auto;
            margin: 0;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo section */
        .logo2 {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            animation: pulse 2s ease-in-out infinite;
            position: relative;
        }

        .logo2::before {
            content: '🔐';
            font-size: 2rem;
        }

        .logo2 img {
            display: none;
            /* Hide original image */
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        /* Title styling */
        .name {
            font-size: 2.5rem !important;
            font-weight: 700 !important;
            background: linear-gradient(135deg, #ffffff, #f0f0f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem !important;
            text-shadow: none !important;
            color: transparent !important;
            text-align: center;
        }

        /* Form styling */
        .form-group {
            margin-bottom: 1.5rem !important;
            animation: fadeInUp 0.8s ease-out;
            animation-fill-mode: both;
        }

        .form-group:nth-child(1) {
            animation-delay: 0.1s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.2s;
        }

        .form-group:nth-child(3) {
            animation-delay: 0.3s;
        }

        .form-group:nth-child(4) {
            animation-delay: 0.4s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Label styling */
        label {
            display: block;
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        /* Input styling */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 12px !important;
            color: white !important;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none !important;
            border-color: rgba(255, 255, 255, 0.4) !important;
            background: rgba(255, 255, 255, 0.15) !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-2px);
        }

        /* Checkbox styling */
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        input[type="checkbox"] {
            width: 20px !important;
            height: 20px;
            margin-right: 0.75rem;
            accent-color: #667eea;
            transform: scale(1.2);
        }

        .form-check-label {
            color: rgba(255, 255, 255, 0.8) !important;
            font-size: 0.9rem;
            cursor: pointer;
            margin-bottom: 0 !important;
        }

        /* Button styling */
        button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            border: none !important;
            border-radius: 12px !important;
            color: white !important;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4) !important;
        }

        button:hover::before {
            left: 100%;
        }

        button:active {
            transform: translateY(0) !important;
        }

        /* Link styling */
        a {
            color: rgba(255, 255, 255, 0.8) !important;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
        }

        a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        a:hover {
            color: white !important;
        }

        a:hover::after {
            width: 100%;
        }

        .text-center {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        /* Alert styling */
        .alert-login {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.3);
            border-radius: 8px;
            padding: 0.75rem;
            margin-top: 1rem;
            color: #ff6b6b;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
            animation: slideIn 0.5s ease-out;
        }

        /* Responsive design */
        @media screen and (max-width: 991px) {
            .centered {
                margin: 0 15px;
                padding: 2rem;
            }

            .name {
                font-size: 2rem !important;
            }
        }

        @media (max-width: 480px) {
            .centered {
                padding: 1.5rem;
            }

            .name {
                font-size: 1.8rem !important;
            }
        }

        /* Laravel component compatibility */
        .text-white {
            color: rgba(255, 255, 255, 0.8) !important;
        }

        /* Additional animations on page load */
        .container {
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
    <title>@yield('title', 'Tiara Group')</title>
    <link rel="icon" type="image/jpeg" href="{{url('/img/company_nobg.png')}}">
</head>

<body>
    <!-- Animated background particles -->
    <div class="bg-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Laravel x-guest-layout equivalent -->
    <div class="container">
        <div class="row pt-5">
            <div class="col-lg-6">

                <div class="centered">
                    <!-- Modern logo -->
                    <div class="logo2">
                        <img src="#" alt="Logo">
                    </div>

                    <!-- Title -->
                    <div class="text-center name">
                        Login
                    </div>

                    <!-- Laravel Form with modern styling -->
                    <form method="POST" action="{{ route('login') }}" class="m-0">
                        @csrf

                        <!-- Username field -->
                        <div class="form-group">
                            <x-label for="username" :value="__('Username')" />
                            <x-input id="username" type="text" name="username" :value="old('username')" required
                                autofocus />
                        </div>

                        <!-- Password field -->
                        <div class="form-group">
                            <x-label for="password" :value="__('Password')" />
                            <x-input id="password" type="password" name="password" required
                                autocomplete="current-password" />
                        </div>

                        <!-- Remember me checkbox -->
                        <div class="form-group">
                            <div class="form-check">
                                <x-checkbox id="remember_me" name="remember" />
                                <label class="form-check-label" for="remember_me">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>

                        <!-- Submit button and forgot password link -->
                        <div>
                            <div class="text-center">
                                @if (Route::has('password.request'))
                                <a class="text-white" href="{{ route('password.request') }}">
                                    {{ __('Forgot your password?') }}
                                </a>
                                @endif
                            </div>
                            <x-button>
                                {{ __('Log in') }}
                            </x-button>
                        </div>
                    </form>

                    <!-- Session status -->
                    <x-auth-session-status :status="session('status')" />
                </div>

                <!-- Validation errors -->
                <x-auth-validation-errors class="alert-login" :errors="$errors" />
            </div>
            <div class="col-lg-6">
            </div>
        </div>
    </div>

    <script>
        // Initialize particles with random positions
        function animateParticles() {
            const particles = document.querySelectorAll('.particle');
            particles.forEach((particle, index) => {
                const randomY = Math.random() * window.innerHeight;
                particle.style.top = randomY + 'px';
            });
        }

        // Add input focus effects
        document.addEventListener('DOMContentLoaded', function() {
            animateParticles();

            // Enhanced focus effects for inputs
            document.querySelectorAll('input[type="text"], input[type="password"]').forEach(input => {
                input.addEventListener('focus', function() {
                    const label = this.parentElement.querySelector('label');
                    if (label) {
                        label.style.color = '#ffffff';
                        label.style.transform = 'translateY(-2px)';
                    }
                });

                input.addEventListener('blur', function() {
                    const label = this.parentElement.querySelector('label');
                    if (label) {
                        label.style.color = 'rgba(255, 255, 255, 0.9)';
                        label.style.transform = 'translateY(0)';
                    }
                });
            });

            // Add loading effect to submit button
            const form = document.querySelector('form');
            const submitButton = document.querySelector('button[type="submit"], x-button button');

            if (form && submitButton) {
                form.addEventListener('submit', function() {
                    submitButton.innerHTML = '<span style="opacity: 0.7;">Signing In...</span>';
                    submitButton.style.opacity = '0.8';
                });
            }
        });

        // Add subtle hover effect to the main container
        document.querySelector('.centered').addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 30px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.1)';
        });

        document.querySelector('.centered').addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.05)';
        });
    </script>
</body>

</html>