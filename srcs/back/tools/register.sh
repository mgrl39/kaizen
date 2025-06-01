curl -X POST http://localhost:8000/api/v1/register -H "Content-Type: application/json" -d '{"username": "testuser", "email": "testuser@example.com", "password": "password123", "name": "Test User"}' | json_pp

php artisan jwt:secret

php artisan config:clear && php artisan cache:clear && curl -X POST http://localhost:8000/api/v1/login -H "Content-Type: application/json" -d '{"identifier": "testuser@example.com", "password": "password123"}' | json_pp