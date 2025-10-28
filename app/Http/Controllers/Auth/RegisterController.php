<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        return view('register');
    }

    /**
     * Handle registration attempt
     */
    public function register(Request $request)
    {
        // Rate limiting by IP
        $key = 'register_attempts:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            Log::warning('Registration rate limit exceeded for IP: ' . $request->ip());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Too many registration attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'
                ], 429);
            }
            
            return back()->withErrors(['email' => 'Too many registration attempts. Please try again later.']);
        }

        // Validate input
        $validationRules = [
            'first_name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'last_name' => ['required', 'string', 'max:50', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ];
        
        // Add validation for security challenge
        if (env('RECAPTCHA_SITE_KEY')) {
            $validationRules['g-recaptcha-response'] = 'sometimes|string';
        } else {
            $validationRules['math_challenge'] = 'required|integer|min:1|max:20';
        }
        
        $validated = $request->validate($validationRules, [
            'first_name.regex' => 'First name may only contain letters, spaces, hyphens, and apostrophes.',
            'last_name.regex' => 'Last name may only contain letters, spaces, hyphens, and apostrophes.',
            'email.unique' => 'This email address is already registered.',
            'password.confirmed' => 'Password confirmation does not match.',
            'terms.accepted' => 'You must accept the terms of service.',
        ]);

        // Verify math challenge if no reCAPTCHA
        if (!env('RECAPTCHA_SITE_KEY')) {
            $mathAnswer = $request->input('math_challenge');
            $correctAnswer = $request->session()->get('math_answer');
            
            if (!$mathAnswer || (int)$mathAnswer !== (int)$correctAnswer) {
                RateLimiter::hit($key, 900); // 15 minutes decay
                
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Please solve the math problem correctly.',
                        'errors' => ['math_challenge' => ['Incorrect answer to the math challenge']]
                    ], 422);
                }
                
                return back()->withErrors(['math_challenge' => 'Incorrect answer to the math challenge']);
            }
        }

        try {
            // Create the user
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(), // Auto-verify for now, implement email verification later
            ]);

            // Log successful registration
            Log::info('New user registered', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Clear rate limiting on successful registration
            RateLimiter::clear($key);

            // Automatically log in the user
            Auth::login($user);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Welcome to our platform.',
                    'redirect' => route('home')
                ]);
            }

            return redirect(route('home'))->with('success', 'Welcome! Your account has been created successfully.');

        } catch (\Exception $e) {
            // Log the error
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'],
                'ip' => $request->ip()
            ]);

            // Hit rate limiter on error
            RateLimiter::hit($key, 900);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Registration failed. Please try again.',
                    'errors' => ['general' => ['An error occurred during registration']]
                ], 500);
            }

            return back()->withErrors(['email' => 'Registration failed. Please try again.']);
        }
    }
}