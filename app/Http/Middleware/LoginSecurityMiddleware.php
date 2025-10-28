<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class LoginSecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Rate Limiting by IP
        $key = 'login_attempts:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            Log::warning('Rate limit exceeded for IP: ' . $request->ip());
            
            return response()->json([
                'error' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minutes.'
            ], 429);
        }

        // 2. Honeypot Check
        if ($request->filled('website')) {
            Log::warning('Bot detected via honeypot from IP: ' . $request->ip());
            
            // Silently reject bots
            return response()->json(['error' => 'Invalid request'], 400);
        }

        // 3. User Agent Check
        $userAgent = $request->userAgent();
        if (empty($userAgent) || $this->isSuspiciousUserAgent($userAgent)) {
            Log::warning('Suspicious user agent detected: ' . $userAgent . ' from IP: ' . $request->ip());
            
            return response()->json(['error' => 'Invalid request'], 400);
        }

        // 4. Check for suspicious patterns
        if ($this->hasSuspiciousPatterns($request)) {
            Log::warning('Suspicious request patterns detected from IP: ' . $request->ip());
            
            return response()->json(['error' => 'Invalid request'], 400);
        }

        // 5. CSRF Token Validation (Laravel handles this automatically, but we can add extra checks)
        if (!$request->hasValidSignature() && $request->isMethod('post')) {
            // Additional CSRF checks can be added here
        }

        // 6. Human Verification (reCAPTCHA or Math Challenge)
        if ($request->isMethod('post')) {
            if (env('RECAPTCHA_SITE_KEY')) {
                // Use reCAPTCHA if configured
                $recaptchaResponse = $request->input('g-recaptcha-response');
                
                if (!$this->verifyRecaptcha($recaptchaResponse, $request->ip())) {
                    RateLimiter::hit($key, 900); // 15 minutes decay
                    
                    return response()->json([
                        'error' => 'Please complete the reCAPTCHA verification.',
                        'errors' => ['g-recaptcha-response' => ['reCAPTCHA verification failed']]
                    ], 422);
                }
            } else {
                // Use math challenge as fallback
                $mathAnswer = $request->input('math_challenge');
                $correctAnswer = $request->session()->get('math_answer');
                
                if (!$mathAnswer || (int)$mathAnswer !== (int)$correctAnswer) {
                    RateLimiter::hit($key, 900); // 15 minutes decay
                    
                    return response()->json([
                        'error' => 'Please solve the math problem correctly.',
                        'errors' => ['math_challenge' => ['Incorrect answer to the math challenge']]
                    ], 422);
                }
            }
        }

        // 7. Input Validation and Sanitization
        if ($request->isMethod('post')) {
            $validationError = $this->validateAndSanitizeInput($request);
            if ($validationError) {
                RateLimiter::hit($key, 900); // 15 minutes decay
                Log::warning('Invalid input detected from IP: ' . $request->ip());
                return $validationError;
            }
        }

        return $next($request);
    }

    /**
     * Check if user agent is suspicious
     */
    private function isSuspiciousUserAgent($userAgent)
    {
        $suspiciousPatterns = [
            'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 
            'python', 'java', 'perl', 'ruby', 'php', 'node',
            'automation', 'test', 'phantom', 'headless'
        ];

        $userAgentLower = strtolower($userAgent);
        
        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($userAgentLower, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for suspicious request patterns
     */
    private function hasSuspiciousPatterns($request)
    {
        // Check for SQL injection patterns
        $inputs = $request->all();
        $sqlPatterns = [
            'union', 'select', 'insert', 'update', 'delete', 'drop', 
            'create', 'alter', 'exec', 'script', 'javascript:', 'vbscript:',
            '<script', '</script>', 'onload=', 'onerror=', 'onclick='
        ];

        foreach ($inputs as $input) {
            if (is_string($input)) {
                $inputLower = strtolower($input);
                foreach ($sqlPatterns as $pattern) {
                    if (strpos($inputLower, $pattern) !== false) {
                        return true;
                    }
                }
            }
        }

        // Check for unusually long inputs
        foreach ($inputs as $input) {
            if (is_string($input) && strlen($input) > 1000) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify reCAPTCHA response
     */
    private function verifyRecaptcha($response, $userIP)
    {
        if (empty($response)) {
            return false;
        }

        $secretKey = env('RECAPTCHA_SECRET_KEY');
        if (empty($secretKey)) {
            // If reCAPTCHA is not configured, skip verification for development
            return env('APP_ENV') === 'local';
        }

        $verifyURL = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $response,
            'remoteip' => $userIP
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($verifyURL, false, $context);
        $responseData = json_decode($result);

        return $responseData->success ?? false;
    }

    /**
     * Validate and sanitize input
     */
    private function validateAndSanitizeInput($request)
    {
        // Email validation
        if ($request->has('email')) {
            $email = filter_var($request->input('email'), FILTER_SANITIZE_EMAIL);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'error' => 'Invalid email format',
                    'errors' => ['email' => ['Please enter a valid email address']]
                ], 422);
            }
        }

        // Password length check
        if ($request->has('password')) {
            $password = $request->input('password');
            if (strlen($password) < 8 || strlen($password) > 255) {
                return response()->json([
                    'error' => 'Invalid password length',
                    'errors' => ['password' => ['Password must be between 8 and 255 characters']]
                ], 422);
            }
        }

        return null; // No validation errors
    }
}