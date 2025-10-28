<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class SecureLoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('index');
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        // Validate input
        $validationRules = [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255',
        ];
        
        // Add validation for security challenge
        if (env('RECAPTCHA_SITE_KEY')) {
            $validationRules['g-recaptcha-response'] = 'sometimes|string';
        } else {
            $validationRules['math_challenge'] = 'required|integer|min:1|max:20';
        }
        
        $request->validate($validationRules);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');
        
        // Create rate limiting key
        $key = 'login_attempts:' . $request->ip();
        
        // Check if user exists
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            // Even if user doesn't exist, hit the rate limiter to prevent user enumeration
            RateLimiter::hit($key, 900); // 15 minutes decay
            
            // Log failed attempt
            Log::warning('Login attempt with non-existent email', [
                'email' => $credentials['email'],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if account is locked
        if ($this->isAccountLocked($user)) {
            Log::warning('Login attempt on locked account', [
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);
            
            throw ValidationException::withMessages([
                'email' => ['This account has been temporarily locked due to security reasons.'],
            ]);
        }

        // Attempt authentication
        if (Auth::attempt($credentials, $remember)) {
            // Clear rate limiting on successful login
            RateLimiter::clear($key);
            
            // Clear failed attempts for this user
            $this->clearFailedAttempts($user);
            
            // Regenerate session ID for security
            $request->session()->regenerate();
            
            // Log successful login
            Log::info('Successful login', [
                'user_id' => $user->id,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            // Update last login timestamp
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);
            
            // Check if request expects JSON (AJAX)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => route('products.list')
                ]);
            }
            
            // Traditional redirect for non-AJAX requests
            return redirect()->intended(route('products.list'))->with('success', 'Login successful');
        }

        // Failed login attempt
        RateLimiter::hit($key, 900); // 15 minutes decay
        $this->recordFailedAttempt($user, $request);
        
        // Log failed attempt
        Log::warning('Failed login attempt', [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);
        
        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * Check if account is locked due to too many failed attempts
     */
    private function isAccountLocked(User $user)
    {
        $maxAttempts = 5;
        $lockoutTime = 30; // minutes
        
        // Check if user has too many failed attempts within the lockout time
        if ($user->failed_attempts >= $maxAttempts && $user->last_failed_attempt) {
            $timeSinceLastAttempt = now()->diffInMinutes($user->last_failed_attempt);
            return $timeSinceLastAttempt < $lockoutTime;
        }
        
        return false;
    }

    /**
     * Record failed login attempt
     */
    private function recordFailedAttempt(User $user, Request $request)
    {
        // You would need to create a failed_login_attempts table and model
        // For now, we'll use a simple approach with user attributes
        
        $failedAttempts = $user->failed_attempts ?? 0;
        $user->update([
            'failed_attempts' => $failedAttempts + 1,
            'last_failed_attempt' => now(),
            'last_failed_ip' => $request->ip()
        ]);
    }

    /**
     * Clear failed attempts on successful login
     */
    private function clearFailedAttempts(User $user)
    {
        $user->update([
            'failed_attempts' => 0,
            'last_failed_attempt' => null,
            'last_failed_ip' => null
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $userId = Auth::id();
            
            // Clear authentication
            Auth::logout();
            
            // Invalidate the session
            $request->session()->invalidate();
            
            // Regenerate the CSRF token
            $request->session()->regenerateToken();
            
            // Clear any remember me cookies
            if ($request->hasCookie(Auth::getRecallerName())) {
                $cookie = cookie()->forget(Auth::getRecallerName());
                return redirect()->route('login')
                    ->with('success', 'You have been logged out successfully.')
                    ->withCookie($cookie);
            }
            
            Log::info('User logged out successfully', [
                'user_id' => $userId,
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('login')->with('success', 'You have been logged out successfully.');
            
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
                'error' => $e->getMessage()
            ]);
            
            // Force logout even if there's an error
            Auth::logout();
            $request->session()->flush();
            
            return redirect()->route('login')->with('error', 'There was an issue logging out, but you have been signed out.');
        }
    }
}