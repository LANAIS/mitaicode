<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create-admin 
                            {name=Admin : Nombre del administrador}
                            {email=admin@mitaicode.com : Email del administrador}
                            {password=password : Contraseña del administrador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un usuario administrador para pruebas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        // Verificar si el usuario ya existe
        $existingUser = User::where('email', $email)->first();
        
        if ($existingUser) {
            $this->info('El usuario con email ' . $email . ' ya existe.');
            return 0;
        }
        
        try {
            // Verificar si existe tabla user_id_seq para generar el user_id
            $hasSequence = DB::select("SHOW TABLES LIKE 'user_id_seq'");
            $userId = 1;
            
            if (!empty($hasSequence)) {
                // Si existe la secuencia, obtener el siguiente valor
                $seqResult = DB::select("SELECT NEXTVAL('user_id_seq') as next_id");
                $userId = $seqResult[0]->next_id;
            } else {
                // Si no hay usuarios, empezar con 1, si hay, usar el siguiente número
                $maxId = DB::table('users')->max('user_id');
                $userId = $maxId ? $maxId + 1 : 1;
            }
            
            // Crear el usuario
            $user = new User();
            $user->user_id = $userId;
            $user->name = $name;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->role = 'admin';
            $user->save();
            
            $this->info('Usuario administrador creado correctamente:');
            $this->line('ID: ' . $user->user_id);
            $this->line('Nombre: ' . $user->name);
            $this->line('Email: ' . $user->email);
            $this->line('Role: ' . $user->role);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error al crear el usuario: ' . $e->getMessage());
            $this->line($e->getTraceAsString());
            return 1;
        }
    }
}
