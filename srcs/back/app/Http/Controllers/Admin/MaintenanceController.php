<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\V1\FunctionController;
use Illuminate\Support\Facades\Http;

class MaintenanceController extends Controller
{
    public function index()
    {
        return view('admin.maintenance');
    }

    public function executeCommand(Request $request)
    {
        $command = $request->input('command');
        $output = '';
        $error = null;

        switch ($command) {
            case 'migrate_fresh':
                $output = $this->captureArtisanOutput('migrate:fresh');
                break;

            case 'seed_rooms':
                $output = $this->captureArtisanOutput('db:seed', ['--class' => 'RoomSeeder']);
                break;

            case 'import_movies':
                $output = $this->captureArtisanOutput('movies:import');
                break;

            case 'db_wipe':
                $output = $this->captureArtisanOutput('db:wipe');
                break;

            case 'generate_functions':
                $date = date('Y-m-d');
                $output = $this->captureArtisanOutput('functions:generate', ['start_date' => $date]);
                break;

            case 'test_functions':
                $functionController = new FunctionController();
                $result = $functionController->getMovieScreenings(4)->getData(true);
                $output = print_r($result, true);
                break;

            case 'create_user':
                try {
                    $user = \App\Models\User::create([
                        'username' => $request->input('username'),
                        'name' => $request->input('name'),
                        'email' => $request->input('email'),
                        'password' => bcrypt($request->input('password')),
                        'role' => 'user'
                    ]);
                    $output = "✅ Usuario creado correctamente:\n";
                    $output .= "ID: {$user->id}\n";
                    $output .= "Usuario: {$user->username}\n";
                    $output .= "Email: {$user->email}\n";
                    $output .= "Rol: {$user->role}\n";
                } catch (\Exception $e) {
                    $error = "Error al crear usuario: " . $e->getMessage();
                }
                break;

            default:
                $error = 'Comando no válido';
        }

        return response()->json([
            'success' => !$error,
            'output' => $output,
            'error' => $error
        ]);
    }

    private function captureArtisanOutput($command, $parameters = [])
    {
        ob_start();
        try {
            Artisan::call($command, $parameters);
            $output = Artisan::output();
        } catch (\Exception $e) {
            $output = "Error: " . $e->getMessage();
        }
        ob_end_clean();
        return $output;
    }
}
