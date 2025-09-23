<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Housesync</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    

</head>
<body>
    <div class="container">
        <div class="auth-wrapper">
            <!-- Left side - Form -->
            <div class="form-section">
                <div class="form-container">
                    <div class="brand">
                        <div class="brand-icon"></div>
                    </div>
                    
                    <h1 class="title">HouSync</h1>
                    <p class="subtitle">Login</p>
                    
                    
                    @if($errors->any())
                        <div class="alert alert-error">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="auth-form" method="POST" action="{{ route('login.post') }}">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email*</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password*</label>
                            <input type="password" id="password" name="password" placeholder="minimum 8 characters" required>
                        </div>
                        
                        <div class="form-options">
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Remember me</label>
                            </div>
                            <a href="#" class="forgot-password">Forgot password?</a>
                        </div>
                        
                        <button type="submit" class="submit-btn">Login</button>
                    </form>
                    
                    <p class="auth-switch">Are you a property owner? <a href="{{ route('landlord.register') }}">Register as Landlord</a></p>
                </div>
            </div>
            
            <!-- Right side - Illustration -->
            <div class="illustration-section">
                <div class="illustration-container">
                    <!-- Isometric illustration elements -->
                    <div class="iso-platform platform-1">
                        <div class="person person-1"></div>
                        <div class="chart chart-1"></div>
                    </div>
                    <div class="iso-platform platform-2">
                        <div class="person person-2"></div>
                        <div class="chart chart-2"></div>
                    </div>
                    <div class="iso-platform platform-3">
                        <div class="person person-3"></div>
                        <div class="device device-1"></div>
                    </div>
                    <div class="iso-platform platform-4">
                        <div class="person person-4"></div>
                        <div class="chart chart-3"></div>
                    </div>
                    <div class="iso-platform platform-5">
                        <div class="person person-5"></div>
                        <div class="chart chart-4"></div>
                    </div>
                    <div class="floating-elements">
                        <div class="cube cube-1"></div>
                        <div class="cube cube-2"></div>
                        <div class="cube cube-3"></div>
                        <div class="cube cube-4"></div>
                        <div class="cube cube-5"></div>
                        <div class="cube cube-6"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Login form is now handled by Laravel backend
    </script>
    
    <style>
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
    
    
</body>
</html> 