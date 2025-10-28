# reCAPTCHA Setup Guide

## Quick Fix for "Invalid site key" Error

The error you're seeing occurs because the reCAPTCHA site key isn't configured. Here are two solutions:

### Option 1: Set up Google reCAPTCHA (Recommended)

1. **Go to Google reCAPTCHA Admin Console:**
   https://www.google.com/recaptcha/admin

2. **Create a new site:**
   - Label: Your App Name (e.g., "Kaisa Login")
   - reCAPTCHA type: Select "reCAPTCHA v2" → "I'm not a robot" Checkbox
   - Domains: Add your domains:
     - For development: `localhost` or `127.0.0.1`
     - For production: `yourdomain.com`
   - Accept terms and submit

3. **Get your keys:**
   - Copy the **Site Key** (starts with `6L...`)
   - Copy the **Secret Key** (starts with `6L...`)

4. **Add to your .env file:**
   ```env
   RECAPTCHA_SITE_KEY=6LcXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
   RECAPTCHA_SECRET_KEY=6LcYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY
   ```

5. **Clear cache:**
   ```bash
   php artisan config:cache
   php artisan cache:clear
   ```

### Option 2: Use Math Challenge (No Setup Required)

If you don't want to set up reCAPTCHA right now, the system will automatically use a simple math challenge instead. This provides basic bot protection without external dependencies.

## Testing Your Setup

### With reCAPTCHA:
1. Visit `/login`
2. You should see the "I'm not a robot" checkbox
3. Try logging in - it should work normally

### With Math Challenge:
1. Don't add reCAPTCHA keys to .env
2. Visit `/login`
3. You should see a simple math problem like "What is 5 + 3?"
4. Enter the correct answer to proceed

## Troubleshooting

### Still seeing "Invalid site key"?
1. Make sure you added the keys to `.env` file (not `.env.example`)
2. Run: `php artisan config:cache`
3. Check that your domain is added in reCAPTCHA admin panel
4. For localhost, make sure you added `localhost` as a domain

### reCAPTCHA not loading?
1. Check browser console for JavaScript errors
2. Ensure you're not blocking Google scripts (ad blockers)
3. Try clearing browser cache

### Math challenge not working?
1. Check browser JavaScript console for errors
2. Make sure sessions are working: `php artisan session:table` then `php artisan migrate`

## Environment Configuration

Add these to your `.env` file:

```env
# Required for reCAPTCHA
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here

# Optional: Session configuration
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_FILES=storage/framework/sessions
SESSION_CONNECTION=null
SESSION_TABLE=sessions
```

## Security Notes

- **Math Challenge**: Provides basic protection against simple bots
- **reCAPTCHA**: Provides advanced protection against sophisticated bots
- Both methods work with the existing rate limiting and security features

The system automatically detects which method to use based on whether reCAPTCHA keys are configured.