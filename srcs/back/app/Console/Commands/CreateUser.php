<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user {--username=} {--name=} {--email=} {--role=user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user interactively';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Crear nuevo usuario ===');
        
        // Recolectar datos interactivamente si no se proporcionaron como opciones
        $userData = [
            'username' => $this->option('username') ?: $this->ask('Nombre de usuario'),
            'name' => $this->option('name') ?: $this->ask('Nombre completo'),
            'email' => $this->option('email') ?: $this->ask('Email'),
            'role' => $this->option('role')
        ];
        
        // Validación inicial de datos
        $validator = Validator::make($userData, [
            'username' => 'required|string|min:3|max:255|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:user,admin'
        ]);
        
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }
        
        // Solicitar contraseña con confirmación
        $password = $this->secret('Contraseña (min. 8 caracteres)');
        $passwordConfirmation = $this->secret('Confirmar contraseña');
        
        if ($password !== $passwordConfirmation) {
            $this->error('Las contraseñas no coinciden');
            return Command::FAILURE;
        }
        
        // Validar la contraseña
        $passwordValidator = Validator::make(
            ['password' => $password],
            ['password' => ['required', 'string', Password::min(8)]]
        );
        
        if ($passwordValidator->fails()) {
            foreach ($passwordValidator->errors()->all() as $error) {
                $this->error($error);
            }
            return Command::FAILURE;
        }
        
        // Mostrar resumen y pedir confirmación
        $this->info('');
        $this->info('=== Resumen de datos ===');
        $this->info("Nombre de usuario: {$userData['username']}");
        $this->info("Nombre completo: {$userData['name']}");
        $this->info("Email: {$userData['email']}");
        $this->info("Rol: {$userData['role']}");
        $this->info("Contraseña: " . str_repeat('*', strlen($password)));
        $this->info('');
        
        if (!$this->confirm('¿Los datos son correctos? Presiona "y" para confirmar', true)) {
            $this->info('Operación cancelada por el usuario');
            return Command::SUCCESS;
        }
        
        try {
            // Crear el usuario
            $user = User::create([
                'username' => $userData['username'],
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($password),
                'role' => $userData['role']
            ]);
            
            $this->info('');
            $this->info('✅ Usuario creado exitosamente');
            $this->info("ID: {$user->id}");
            $this->info("Usuario: {$user->username}");
            $this->info("Email: {$user->email}");
            $this->info("Rol: {$user->role}");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error al crear el usuario: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 