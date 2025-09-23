<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Housesync</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="auth-wrapper register-wrapper">
            <!-- Left side - Illustration -->
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
            
            <!-- Right side - Form -->
            <div class="form-section">
                <div class="form-container">
                    <div class="brand">
                        <div class="brand-icon"></div>
                    </div>
                    
                    <h1 class="title">Register</h1>
                    <p class="subtitle">Manage all your inventory efficiently</p>
                    <p class="description">Let's get you all set up so you can verify your personal account and begin setting up your work profile</p>
                    
                    <form class="auth-form register-form" method="POST" action="{{ route('register.post') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First name</label>
                                <input type="text" id="first_name" name="first_name" placeholder="Enter your name">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last name</label>
                                <input type="text" id="last_name" name="last_name" placeholder="minimum 8 characters">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" placeholder="Enter your email">
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone no.</label>
                                <input type="tel" id="phone" name="phone" placeholder="minimum 8 characters">
                            </div>
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your email">
                        </div>
                        
                        <div class="form-options">
                            <div class="terms-agreement">
                                <input type="checkbox" id="terms" name="terms">
                                <label for="terms">I agree to all terms, privacy policies, and fees</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="submit-btn">Sign up</button>
                    </form>
                    
                    <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Registration form is handled by Laravel backend
    </script>
    
    
</body>
</html> 