@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">
                    <i class="fas fa-user-lock me-2"></i>
                    Login
                </h4>
            </div>
            <div class="card-body p-3">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="/login" id="loginForm">
                    @csrf
                    
                    <!-- Honeypot field for bot detection -->
                    <input type="text" name="website" style="display:none;" tabindex="-1" autocomplete="off">
                    
                    <div class="mb-2">
                        <label for="email" class="form-label mb-1">
                            <i class="fas fa-envelope me-1"></i>
                            Email Address
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email" 
                               autofocus
                               maxlength="255"
                               pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                               placeholder="Enter your email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-2">
                        <label for="password" class="form-label mb-1">
                            <i class="fas fa-lock me-1"></i>
                            Password
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password"
                                   minlength="8"
                                   maxlength="255"
                                   placeholder="Enter your password (min 8 chars)">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Google reCAPTCHA (Only show if configured) -->
                    @if(env('RECAPTCHA_SITE_KEY'))
                        <div class="mb-2">
                            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                            @error('g-recaptcha-response')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @else
                        <!-- Alternative Security Challenge -->
                        <div class="mb-2">
                            <div class="alert alert-warning border-0 py-2" style="background-color: rgba(255, 193, 7, 0.1);">
                                @php
                                    $num1 = rand(1, 10);
                                    $num2 = rand(1, 10);
                                    $answer = $num1 + $num2;
                                    session(['math_answer' => $answer]);
                                @endphp
                                <small>
                                    <i class="fas fa-shield-alt text-warning me-1"></i>
                                    <strong>Security:</strong> What is {{ $num1 }} + {{ $num2 }}?
                                </small>
                                <input type="number" 
                                       class="form-control form-control-sm mt-1 @error('math_challenge') is-invalid @enderror" 
                                       id="math_challenge" 
                                       name="math_challenge" 
                                       required 
                                       min="1" 
                                       max="20"
                                       placeholder="Enter answer">
                                @error('math_challenge')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Remember me and Login button row -->
                    <div class="row align-items-center mb-2">
                        <div class="col-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">
                                    Remember me
                                </label>
                            </div>
                        </div>
                        <div class="col-6 text-end">
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                <span id="attemptCount">0</span>/5 attempts
                            </small>
                        </div>
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-primary" id="loginBtn">
                            <span class="btn-text">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                Login
                            </span>
                            <span class="btn-loading d-none">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                Verifying...
                            </span>
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <small class="d-block mb-1">
                        <a href="/forgot-password" class="text-decoration-none">
                            <i class="fas fa-key me-1"></i>
                            Forgot password?
                        </a>
                    </small>
                    <small>
                        <a href="/register" class="text-decoration-none">
                            <i class="fas fa-user-plus me-1"></i>
                            Create account
                        </a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="errorModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Login Error
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="error-icon mb-3">
                    <i class="fas fa-times-circle text-danger" style="font-size: 3rem;"></i>
                </div>
                <div id="errorMessage" class="text-dark">
                    <!-- Error message will be inserted here -->
                </div>
                <div id="errorDetails" class="mt-3 text-muted small">
                    <!-- Additional error details will be inserted here -->
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="tryAgainBtn" data-bs-dismiss="modal">
                    <i class="fas fa-redo me-2"></i>
                    Try Again
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    body {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #2563eb 100%);
        min-height: 100vh;
        position: relative;
    }
    
    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 80%, rgba(96, 165, 250, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(147, 197, 253, 0.05) 0%, transparent 50%);
        pointer-events: none;
    }
    
    .card {
        border-radius: 20px;
        backdrop-filter: blur(15px);
        background-color: rgba(255, 255, 255, 0.95);
        box-shadow: 0 20px 40px rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(147, 197, 253, 0.2);
        position: relative;
        z-index: 1;
    }
    
    .card-header {
        border-radius: 20px 20px 0 0 !important;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .card-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
        animation: shimmer 3s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        transform: translateY(-1px);
        transition: all 0.3s ease;
    }
    
    .form-control {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }
    
    .form-control:hover {
        border-color: #bfdbfe;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-primary:hover::before {
        left: 100%;
    }
    
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    }
    
    .alert {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%) !important;
        color: #92400e;
        border-left: 4px solid #f59e0b;
    }
    
    .input-group-text, .btn-outline-secondary {
        border-color: #bfdbfe;
        background-color: #eff6ff;
        color: #1e40af;
        transition: all 0.3s ease;
    }
    
    .btn-outline-secondary:hover {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: white;
        transform: scale(1.05);
    }
    
    .bg-light {
        background: rgba(239, 246, 255, 0.8) !important;
        border-radius: 10px;
        border: 1px solid rgba(147, 197, 253, 0.2);
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 20px;
        backdrop-filter: blur(15px);
        background-color: rgba(255, 255, 255, 0.95);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(147, 197, 253, 0.1);
    }
    
    .modal-header {
        border-radius: 20px 20px 0 0 !important;
    }
    
    /* Enhanced text colors */
    .text-success {
        color: #2563eb !important;
    }
    
    .text-muted {
        color: #6b7280 !important;
    }
    
    /* Link styling */
    a {
        color: #3b82f6;
        transition: color 0.3s ease;
    }
    
    a:hover {
        color: #2563eb;
        text-decoration: none !important;
    }
    
    /* Rate limiting display */
    #attemptCount {
        color: #3b82f6;
        font-weight: 600;
    }
    
    .error-icon {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .modal-backdrop {
        backdrop-filter: blur(5px);
    }
    
    .modal-dialog-centered {
        animation: slideInDown 0.3s ease-out;
    }
    
    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translate3d(0, -100%, 0);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
</style>

<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Google reCAPTCHA (Only load if configured) -->
@if(env('RECAPTCHA_SITE_KEY'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<!-- Security JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        const email = document.getElementById('email');
        
        let loginAttempts = parseInt(localStorage.getItem('loginAttempts') || '0');
        let lastAttemptTime = parseInt(localStorage.getItem('lastAttemptTime') || '0');
        
        // Update attempt counter display
        function updateAttemptDisplay() {
            const attemptCount = document.getElementById('attemptCount');
            if (attemptCount) {
                attemptCount.textContent = loginAttempts;
            }
        }
        
        // Check if user is rate limited
        function isRateLimited() {
            const now = Date.now();
            const timeDiff = now - lastAttemptTime;
            const cooldownPeriod = 15 * 60 * 1000; // 15 minutes
            
            if (loginAttempts >= 5 && timeDiff < cooldownPeriod) {
                const remainingTime = Math.ceil((cooldownPeriod - timeDiff) / 60000);
                return remainingTime;
            }
            
            // Reset attempts after cooldown
            if (timeDiff >= cooldownPeriod) {
                loginAttempts = 0;
                localStorage.setItem('loginAttempts', '0');
            }
            
            return false;
        }
        
        // Password toggle functionality
        if (togglePassword && password && toggleIcon) {
            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                
                if (type === 'password') {
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                } else {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                }
            });
        }
        
        // Form submission security with AJAX
        if (loginForm && loginBtn) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Always prevent default to use AJAX
                
                const rateLimitTime = isRateLimited();
                
                if (rateLimitTime) {
                    showErrorModal(
                        'Rate Limit Exceeded', 
                        `Too many failed attempts. Please try again in ${rateLimitTime} minutes.`,
                        'warning'
                    );
                    return false;
                }
                
                // Check honeypot
                const honeypot = document.querySelector('input[name="website"]');
                if (honeypot && honeypot.value !== '') {
                    console.log('Bot detected via honeypot');
                    showErrorModal('Security Alert', 'Suspicious activity detected.', 'danger');
                    return false;
                }
                
                // Show loading state
                const btnText = loginBtn.querySelector('.btn-text');
                const btnLoading = loginBtn.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.classList.add('d-none');
                    btnLoading.classList.remove('d-none');
                    loginBtn.disabled = true;
                }
                
                // Submit form via AJAX
                submitLoginForm();
            });
        }
        
        // AJAX form submission
        function submitLoginForm() {
            const formData = new FormData(loginForm);
            
            fetch('/login', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    return response.json().then(data => Promise.reject(data));
                }
            })
            .then(data => {
                // Success - redirect or show success message
                if (data.success) {
                    // Clear failed attempts on success
                    localStorage.setItem('loginAttempts', '0');
                    localStorage.setItem('lastAttemptTime', '0');
                    
                    // Show success message briefly then redirect
                    showSuccessModal('Login Successful!', 'Welcome back!');
                    
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 1500);
                }
            })
            .catch(error => {
                // Handle errors
                resetLoadingState();
                
                // Increment attempt counter
                loginAttempts++;
                lastAttemptTime = Date.now();
                localStorage.setItem('loginAttempts', loginAttempts.toString());
                localStorage.setItem('lastAttemptTime', lastAttemptTime.toString());
                updateAttemptDisplay();
                
                // Show error modal
                let errorMessage = 'An unexpected error occurred. Please try again.';
                let errorDetails = '';
                
                if (error.error) {
                    errorMessage = error.error;
                }
                
                if (error.errors) {
                    const errorList = [];
                    for (const [field, messages] of Object.entries(error.errors)) {
                        errorList.push(...messages);
                    }
                    errorDetails = errorList.join('<br>');
                }
                
                showErrorModal('Login Failed', errorMessage, 'danger', errorDetails);
            });
        }
        
        // Reset loading state
        function resetLoadingState() {
            const btnText = loginBtn.querySelector('.btn-text');
            const btnLoading = loginBtn.querySelector('.btn-loading');
            
            if (btnText && btnLoading) {
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                loginBtn.disabled = false;
            }
        }
        
        // Show error modal
        function showErrorModal(title, message, type = 'danger', details = '') {
            const modal = document.getElementById('errorModal');
            const modalTitle = document.getElementById('errorModalLabel');
            const errorMessage = document.getElementById('errorMessage');
            const errorDetails = document.getElementById('errorDetails');
            const modalHeader = modal.querySelector('.modal-header');
            const errorIcon = modal.querySelector('.error-icon i');
            
            // Update modal content
            modalTitle.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${title}`;
            errorMessage.innerHTML = message;
            errorDetails.innerHTML = details;
            
            // Update colors based on type
            if (type === 'warning') {
                modalHeader.className = 'modal-header bg-warning text-dark border-0';
                errorIcon.className = 'fas fa-exclamation-triangle text-warning';
            } else {
                modalHeader.className = 'modal-header bg-danger text-white border-0';
                errorIcon.className = 'fas fa-times-circle text-danger';
            }
            
            // Show modal
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }
        
        // Show success modal
        function showSuccessModal(title, message) {
            const modal = document.getElementById('errorModal');
            const modalTitle = document.getElementById('errorModalLabel');
            const errorMessage = document.getElementById('errorMessage');
            const errorDetails = document.getElementById('errorDetails');
            const modalHeader = modal.querySelector('.modal-header');
            const errorIcon = modal.querySelector('.error-icon i');
            const tryAgainBtn = document.getElementById('tryAgainBtn');
            
            // Update modal content for success
            modalTitle.innerHTML = `<i class="fas fa-check-circle me-2"></i>${title}`;
            errorMessage.innerHTML = message;
            errorDetails.innerHTML = '';
            
            // Update colors for success
            modalHeader.className = 'modal-header bg-success text-white border-0';
            errorIcon.className = 'fas fa-check-circle text-success';
            
            // Hide try again button for success
            tryAgainBtn.style.display = 'none';
            
            // Show modal
            const bootstrapModal = new bootstrap.Modal(modal);
            bootstrapModal.show();
        }
        
        // Try Again button functionality
        document.getElementById('tryAgainBtn').addEventListener('click', function() {
            // Clear form errors
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            
            // Focus on email field
            if (email) {
                email.focus();
            }
            
            // Show try again button again (in case it was hidden)
            this.style.display = 'inline-block';
        });
        
        // Real-time input validation
        if (email) {
            email.addEventListener('input', function() {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                if (this.value && !emailRegex.test(this.value)) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
        
        if (password) {
            password.addEventListener('input', function() {
                if (this.value.length > 0 && this.value.length < 8) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
        
        // Initialize display
        updateAttemptDisplay();
        
        // Check rate limit on page load
        const rateLimitTime = isRateLimited();
        if (rateLimitTime && loginBtn) {
            loginBtn.disabled = true;
            loginBtn.innerHTML = `<i class="fas fa-clock me-2"></i>Locked (${rateLimitTime}m remaining)`;
            loginBtn.classList.add('btn-secondary');
            loginBtn.classList.remove('btn-primary');
        }
        
        // Prevent copy/paste in password field for added security
        if (password) {
            password.addEventListener('paste', function(e) {
                e.preventDefault();
                alert('Pasting is disabled for security reasons. Please type your password.');
            });
        }
        
        // Detect suspicious behavior
        let keyPressCount = 0;
        let rapidClicks = 0;
        
        document.addEventListener('keydown', function() {
            keyPressCount++;
            if (keyPressCount > 100) { // Unusually high key presses
                console.log('Suspicious activity detected: High key press count');
            }
        });
        
        loginBtn?.addEventListener('click', function() {
            rapidClicks++;
            setTimeout(() => rapidClicks--, 1000);
            
            if (rapidClicks > 5) {
                console.log('Suspicious activity detected: Rapid clicking');
                this.disabled = true;
                setTimeout(() => {
                    this.disabled = false;
                }, 5000);
            }
        });
    });
</script>
@endsection
