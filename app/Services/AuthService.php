<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\DTOs\UserDTO;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        $user = $this->userRepository->create($data);
        $token = JWTAuth::fromUser($user);
        
        return [
            'user' => new UserDTO($user),
            'token' => $token
        ];
    }

    public function login(string $email, string $password)
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception('Credenciales inválidas');
        }

        $token = JWTAuth::fromUser($user);
        
        return [
            'user' => new UserDTO($user),
            'token' => $token
        ];
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    public function getAuthenticatedUser()
    {
        $user = JWTAuth::parseToken()->authenticate();
        return new UserDTO($user);
    }
} 