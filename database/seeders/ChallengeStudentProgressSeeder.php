<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ChallengeStudentProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar cualquier dato existente
        DB::table('challenge_student_progress')->truncate();
        
        // Ejemplo de progreso para diferentes estudiantes en diferentes desafíos
        $progress_data = [
            [
                'challenge_id' => 1,
                'student_id' => 17,
                'current_exercise' => 3,
                'total_exercises' => 5,
                'attempts' => 2,
                'score' => 80,
                'progress_percentage' => 60.00,
                'started_at' => Carbon::now()->subDays(2),
                'last_activity_at' => Carbon::now()->subDay(),
                'is_completed' => false,
                'status' => 'in_progress',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(1)
            ],
            [
                'challenge_id' => 2,
                'student_id' => 17,
                'current_exercise' => 5,
                'total_exercises' => 5,
                'attempts' => 4,
                'score' => 95,
                'progress_percentage' => 100.00,
                'started_at' => Carbon::now()->subDays(5),
                'last_activity_at' => Carbon::now()->subDays(3),
                'completed_at' => Carbon::now()->subDays(3),
                'is_completed' => true,
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(3)
            ],
            [
                'challenge_id' => 3,
                'student_id' => 17,
                'current_exercise' => 2,
                'total_exercises' => 4,
                'attempts' => 1,
                'score' => 35,
                'progress_percentage' => 25.00,
                'started_at' => Carbon::now()->subDay(),
                'last_activity_at' => Carbon::now()->subHours(6),
                'is_completed' => false,
                'status' => 'in_progress',
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subHours(6)
            ],
            [
                'challenge_id' => 1,
                'student_id' => 18,
                'current_exercise' => 5,
                'total_exercises' => 5,
                'attempts' => 3,
                'score' => 92,
                'progress_percentage' => 100.00,
                'started_at' => Carbon::now()->subDays(4),
                'last_activity_at' => Carbon::now()->subDays(2),
                'completed_at' => Carbon::now()->subDays(2),
                'is_completed' => true,
                'status' => 'completed',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(2)
            ],
            [
                'challenge_id' => 4,
                'student_id' => 17,
                'current_exercise' => 1,
                'total_exercises' => 6,
                'attempts' => 0,
                'score' => 0,
                'progress_percentage' => 0.00,
                'started_at' => null,
                'last_activity_at' => null,
                'is_completed' => false,
                'status' => 'not_started',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        // Insertar los datos de progreso en la base de datos
        foreach ($progress_data as $progress) {
            DB::table('challenge_student_progress')->insert($progress);
        }
    }
}
