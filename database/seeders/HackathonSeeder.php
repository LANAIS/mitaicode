<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Hackathon;
use Carbon\Carbon;

class HackathonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar la estructura de la tabla hackathons
        $hasCreatedByColumn = Schema::hasColumn('hackathons', 'created_by');
        
        // Crear profesores
        $teachers = [
            [
                'username' => 'maria.garcia',
                'first_name' => 'María',
                'last_name' => 'García',
                'email' => 'maria.garcia@universidad.edu',
                'password' => Hash::make('password123'),
                'role' => 'teacher'
            ],
            [
                'username' => 'juan.perez',
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'email' => 'juan.perez@universidad.edu',
                'password' => Hash::make('password123'),
                'role' => 'teacher'
            ],
            [
                'username' => 'ana.martinez',
                'first_name' => 'Ana',
                'last_name' => 'Martínez',
                'email' => 'ana.martinez@universidad.edu',
                'password' => Hash::make('password123'),
                'role' => 'teacher'
            ]
        ];

        foreach ($teachers as $teacher) {
            User::firstOrCreate(['email' => $teacher['email']], $teacher);
        }

        // Crear estudiantes
        $students = [
            [
                'username' => 'carlos.lopez',
                'first_name' => 'Carlos',
                'last_name' => 'López',
                'email' => 'carlos.lopez@estudiante.edu',
                'password' => Hash::make('password123'),
                'role' => 'student'
            ],
            [
                'username' => 'laura.sanchez',
                'first_name' => 'Laura',
                'last_name' => 'Sánchez',
                'email' => 'laura.sanchez@estudiante.edu',
                'password' => Hash::make('password123'),
                'role' => 'student'
            ],
            [
                'username' => 'miguel.torres',
                'first_name' => 'Miguel',
                'last_name' => 'Torres',
                'email' => 'miguel.torres@estudiante.edu',
                'password' => Hash::make('password123'),
                'role' => 'student'
            ],
            [
                'username' => 'sofia.ramirez',
                'first_name' => 'Sofia',
                'last_name' => 'Ramírez',
                'email' => 'sofia.ramirez@estudiante.edu',
                'password' => Hash::make('password123'),
                'role' => 'student'
            ]
        ];

        foreach ($students as $student) {
            User::firstOrCreate(['email' => $student['email']], $student);
        }

        // Crear hackathones
        $hackathons = [
            [
                'title' => 'Innovación en IA 2024',
                'description' => 'Hackathon enfocado en el desarrollo de soluciones innovadoras utilizando Inteligencia Artificial',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(45),
                'max_participants' => 30,
                'max_teams' => 6,
                'team_size' => 5,
                'status' => 'pending'
            ],
            [
                'title' => 'Desarrollo Sostenible',
                'description' => 'Hackathon para crear soluciones tecnológicas que aborden los Objetivos de Desarrollo Sostenible',
                'start_date' => Carbon::now()->subDays(15),
                'end_date' => Carbon::now()->addDays(15),
                'max_participants' => 25,
                'max_teams' => 5,
                'team_size' => 5,
                'status' => 'active'
            ],
            [
                'title' => 'Blockchain Challenge 2023',
                'description' => 'Hackathon sobre aplicaciones descentralizadas y tecnología blockchain',
                'start_date' => Carbon::now()->subDays(60),
                'end_date' => Carbon::now()->subDays(45),
                'max_participants' => 20,
                'max_teams' => 4,
                'team_size' => 5,
                'status' => 'finished'
            ]
        ];

        $teachers = User::where('role', 'teacher')->get();
        
        foreach ($hackathons as $index => $hackathonData) {
            // Añadir created_by solo si la columna existe
            if ($hasCreatedByColumn && isset($teachers[$index])) {
                $hackathonData['created_by'] = $teachers[$index]->user_id;
            }
            
            Hackathon::create($hackathonData);
        }
    }
} 