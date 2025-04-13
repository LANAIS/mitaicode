<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existe algún usuario
        $userExists = DB::table('users')->exists();
        
        if ($userExists) {
            $this->command->info('Ya existen usuarios en la tabla. No se crearán nuevos usuarios.');
            return;
        }
        
        // Crear usuario administrador
        DB::table('users')->insert([
            'user_id' => 1,
            'username' => 'admin',
            'email' => 'admin@mitaicode.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'MitaiCode',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->command->info('Usuario administrador creado correctamente.');
    }
}
