<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Registration - HouseSync</title>
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
                    
                    <h1 class="title">Create your Tenant Account</h1>
                    <p class="subtitle">Access your tenant dashboard, upload documents, and manage your lease</p>

                    @if($errors->any())
                        <div class="alert alert-error">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form class="auth-form register-form" method="POST" action="{{ route('register.post') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First name</label>
                                <div class="input-with-icon">
                                    <span class="input-icon"><i class="fas fa-user"></i></span>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Juan" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last name</label>
                                <div class="input-with-icon">
                                    <span class="input-icon"><i class="fas fa-user"></i></span>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Dela Cruz" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <div class="input-with-icon">
                                    <span class="input-icon"><i class="fas fa-envelope"></i></span>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone number</label>
                                <div class="input-with-icon">
                                    <span class="input-icon"><i class="fas fa-phone"></i></span>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="e.g. +63 912 345 6789">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-with-icon">
                                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="password" name="password" placeholder="At least 8 characters" minlength="8" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <div class="input-with-icon">
                                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" minlength="8" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-options">
                            <div class="terms-agreement">
                                <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                            </div>
                        </div>
                        
                        <button type="submit" class="submit-btn">Create Tenant Account</button>
                    </form>
                    
                    <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js" defer></script>
    <script>
        function togglePassword(id) {
            const input = document.getElementById(id);
            const btn = input.parentElement.querySelector('.password-toggle i');
            if (input.type === 'password') {
                input.type = 'text';
                btn.classList.remove('fa-eye');
                btn.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                btn.classList.remove('fa-eye-slash');
                btn.classList.add('fa-eye');
            }
        }
    </script>
    
    
</body>
</html> 