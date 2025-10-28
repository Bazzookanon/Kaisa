@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    Create Account
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

                <form method="POST" action="/register" id="registerForm">
                    @csrf
                    
                    <!-- Honeypot field for bot detection -->
                    <input type="text" name="website" style="display:none;" tabindex="-1" autocomplete="off">
                    
                    <!-- Name Fields Row -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="first_name" class="form-label mb-1">
                                    <i class="fas fa-user me-1"></i>
                                    First Name
                                </label>
                                <input type="text" 
                                       class="form-control @error('first_name') is-invalid @enderror" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="{{ old('first_name') }}" 
                                       required 
                                       autocomplete="given-name" 
                                       autofocus
                                       maxlength="50"
                                       placeholder="Enter first name">
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <label for="last_name" class="form-label mb-1">
                                    <i class="fas fa-user me-1"></i>
                                    Last Name
                                </label>
                                <input type="text" 
                                       class="form-control @error('last_name') is-invalid @enderror" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="{{ old('last_name') }}" 
                                       required 
                                       autocomplete="family-name"
                                       maxlength="50"
                                       placeholder="Enter last name">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

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
                                   autocomplete="new-password"
                                   minlength="8"
                                   maxlength="255"
                                   placeholder="Create password (min 8 chars)">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <!-- Password Strength Indicator -->
                        <div class="mt-1">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar" id="passwordStrength" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="text-muted" id="passwordStrengthText">Password strength</small>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="password_confirmation" class="form-label mb-1">
                            <i class="fas fa-lock me-1"></i>
                            Confirm Password
                        </label>
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   id="password_confirmation" 
                                   name="password_confirmation" 
                                   required 
                                   autocomplete="new-password"
                                   minlength="8"
                                   maxlength="255"
                                   placeholder="Confirm your password">
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                <i class="fas fa-eye" id="toggleIconConfirm"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="passwordMatchText"></small>
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

                    <!-- Terms and Privacy -->
                    <div class="mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms" name="terms" required {{ old('terms') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="terms">
                                I agree to the <a href="/terms" target="_blank" class="text-decoration-none">Terms of Service</a> 
                                and <a href="/privacy" target="_blank" class="text-decoration-none">Privacy Policy</a>
                            </label>
                            @error('terms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Newsletter Subscription -->
                    <div class="mb-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="newsletter" name="newsletter" {{ old('newsletter') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="newsletter">
                                <i class="fas fa-envelope me-1"></i>
                                Subscribe to newsletter for updates and news
                            </label>
                        </div>
                    </div>

                    <div class="d-grid mb-2">
                        <button type="submit" class="btn btn-primary" id="registerBtn">
                            <span class="btn-text">
                                <i class="fas fa-user-plus me-2"></i>
                                Create Account
                            </span>
                            <span class="btn-loading d-none">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                Creating Account...
                            </span>
                        </button>
                    </div>
                </form>

                <div class="text-center">
                    <small>
                        Already have an account? 
                        <a href="/login" class="text-decoration-none fw-bold">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Sign in here
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
                    Registration Error
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
    
    /* Password strength indicator */
    .progress-bar {
        transition: all 0.3s ease;
    }
    
    .progress-bar.bg-danger {
        background-color: #dc3545 !important;
    }
    
    .progress-bar.bg-warning {
        background-color: #ffc107 !important;
    }
    
    .progress-bar.bg-info {
        background-color: #0dcaf0 !important;
    }
    
    .progress-bar.bg-success {
        background-color: #198754 !important;
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

<!-- Registration JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const registerForm = document.getElementById('registerForm');
        const registerBtn = document.getElementById('registerBtn');
        const togglePassword = document.getElementById('togglePassword');
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirmation');
        const toggleIcon = document.getElementById('toggleIcon');
        const toggleIconConfirm = document.getElementById('toggleIconConfirm');
        const email = document.getElementById('email');
        const firstName = document.getElementById('first_name');
        const lastName = document.getElementById('last_name');
        
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
        
        // Password confirmation toggle functionality
        if (togglePasswordConfirm && passwordConfirm && toggleIconConfirm) {
            togglePasswordConfirm.addEventListener('click', function() {
                const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirm.setAttribute('type', type);
                
                if (type === 'password') {
                    toggleIconConfirm.classList.remove('fa-eye-slash');
                    toggleIconConfirm.classList.add('fa-eye');
                } else {
                    toggleIconConfirm.classList.remove('fa-eye');
                    toggleIconConfirm.classList.add('fa-eye-slash');
                }
            });
        }
        
        // Password strength checker
        if (password) {
            password.addEventListener('input', function() {
                const pwd = this.value;
                const strengthBar = document.getElementById('passwordStrength');
                const strengthText = document.getElementById('passwordStrengthText');
                
                let strength = 0;
                let strengthLabel = 'Very Weak';
                let strengthClass = 'bg-danger';
                
                // Check password criteria
                if (pwd.length >= 8) strength += 20;
                if (pwd.match(/[a-z]/)) strength += 20;
                if (pwd.match(/[A-Z]/)) strength += 20;
                if (pwd.match(/[0-9]/)) strength += 20;
                if (pwd.match(/[^a-zA-Z0-9]/)) strength += 20;
                
                // Set strength label and class
                if (strength >= 80) {
                    strengthLabel = 'Very Strong';
                    strengthClass = 'bg-success';
                } else if (strength >= 60) {
                    strengthLabel = 'Strong';
                    strengthClass = 'bg-info';
                } else if (strength >= 40) {
                    strengthLabel = 'Medium';
                    strengthClass = 'bg-warning';
                } else if (strength >= 20) {
                    strengthLabel = 'Weak';
                    strengthClass = 'bg-warning';
                }
                
                strengthBar.style.width = strength + '%';
                strengthBar.className = 'progress-bar ' + strengthClass;
                strengthText.textContent = strengthLabel;
                
                // Validation styling
                if (pwd.length > 0 && pwd.length < 8) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
        
        // Password confirmation checker
        if (passwordConfirm && password) {
            passwordConfirm.addEventListener('input', function() {
                const matchText = document.getElementById('passwordMatchText');
                
                if (this.value && password.value) {
                    if (this.value === password.value) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                        matchText.innerHTML = '<i class="fas fa-check text-success me-1"></i>Passwords match';
                        matchText.classList.remove('text-danger');
                        matchText.classList.add('text-success');
                    } else {
                        this.classList.remove('is-valid');
                        this.classList.add('is-invalid');
                        matchText.innerHTML = '<i class="fas fa-times text-danger me-1"></i>Passwords do not match';
                        matchText.classList.remove('text-success');
                        matchText.classList.add('text-danger');
                    }
                } else {
                    this.classList.remove('is-invalid', 'is-valid');
                    matchText.textContent = '';
                }
            });
        }
        
        // Form submission with AJAX
        if (registerForm && registerBtn) {
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Always prevent default to use AJAX
                
                // Check honeypot
                const honeypot = document.querySelector('input[name="website"]');
                if (honeypot && honeypot.value !== '') {
                    console.log('Bot detected via honeypot');
                    showErrorModal('Security Alert', 'Suspicious activity detected.', 'danger');
                    return false;
                }
                
                // Show loading state
                const btnText = registerBtn.querySelector('.btn-text');
                const btnLoading = registerBtn.querySelector('.btn-loading');
                
                if (btnText && btnLoading) {
                    btnText.classList.add('d-none');
                    btnLoading.classList.remove('d-none');
                    registerBtn.disabled = true;
                }
                
                // Submit form via AJAX
                submitRegisterForm();
            });
        }
        
        // AJAX form submission
        function submitRegisterForm() {
            const formData = new FormData(registerForm);
            
            fetch('/register', {
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
                    // Show success message briefly then redirect
                    showSuccessModal('Registration Successful!', 'Welcome! Your account has been created.');
                    
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '/login';
                        }
                    }, 2000);
                }
            })
            .catch(error => {
                // Handle errors
                resetLoadingState();
                
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
                
                showErrorModal('Registration Failed', errorMessage, 'danger', errorDetails);
            });
        }
        
        // Reset loading state
        function resetLoadingState() {
            const btnText = registerBtn.querySelector('.btn-text');
            const btnLoading = registerBtn.querySelector('.btn-loading');
            
            if (btnText && btnLoading) {
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
                registerBtn.disabled = false;
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
            
            // Focus on first name field
            if (firstName) {
                firstName.focus();
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
        
        // Name validation
        [firstName, lastName].forEach(field => {
            if (field) {
                field.addEventListener('input', function() {
                    const nameRegex = /^[a-zA-Z\s'-]+$/;
                    if (this.value && !nameRegex.test(this.value)) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                });
            }
        });
    });
</script>
@endsection
