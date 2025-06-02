# JWT Secret Key Documentation

## Purpose
The JWT secret key is used for signing JSON Web Tokens in Laravel applications. It's essential for secure authentication.

## When to Use
- Initial setup
- When experiencing authentication issues
- After system migrations
- If security has been compromised

## Command
```bash
php artisan jwt:secret
```

## Effects
- Generates a new secret key in .env file
- Invalidates all existing tokens
- Forces users to re-login

## Security Note
Keep this key secure and never commit it to version control.
