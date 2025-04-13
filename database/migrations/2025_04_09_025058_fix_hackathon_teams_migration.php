<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Deshabilitamos las migraciones problemáticas marcándolas como ya ejecutadas
        $problematicMigrations = [
            '2023_07_01_000000_add_is_winner_to_hackathon_teams_table',
            '2023_07_06_create_hackathon_deliverables_table',
            '2023_07_07_create_hackathon_messages_table',
            '2023_09_19_create_team_deliverables_table',
            '2023_09_19_create_team_messages_table',
            '2023_10_20_100000_create_site_settings_table',
            '2023_12_10_000000_create_challenges_table',
            '2023_12_10_000001_create_challenge_exercises_table',
            '2023_12_10_000002_create_challenge_submissions_table',
            '2023_12_10_000003_add_last_login_at_to_users_table',
            '2023_12_10_000004_create_challenge_user_table',
            '2023_12_10_000005_create_hackathon_team_members_table',
            '2023_12_10_000005_create_hackathon_teams_table',
            '2023_12_10_000006_create_hackathons_table',
            '2023_12_10_000007_add_purchases_to_store_items_table',
            '2023_12_10_000008_add_status_to_challenge_user_table',
            '2023_12_12_000001_create_email_notifications_table',
            '2025_04_06_215755_create_student_profiles_table',
            '2025_04_06_215800_create_teacher_profiles_table',
            '2025_04_06_215818_create_classrooms_table',
            '2025_04_06_215832_create_class_enrollments_table',
            '2025_04_06_215836_create_projects_table',
            '2025_04_06_215845_create_mission_progress_table',
            '2025_04_06_215849_create_badges_table',
            '2025_04_06_215856_create_user_badges_table',
            '2025_04_07_000001_create_challenge_progress_table',
            '2025_04_07_100812_create_hackathons_table',
            '2025_04_07_100925_create_hackathon_teams_table',
            '2025_04_07_101000_create_hackathon_rounds_table',
            '2025_04_07_101025_create_hackathon_judges_table',
            '2025_04_07_101050_create_hackathon_deliverables_table',
            '2025_04_07_101100_create_hackathon_messages_table',
            '2025_04_07_170853_create_prompt_engineering_tables',
            '2025_04_07_174052_create_teacher_challenges_tables',
            '2025_04_07_201652_create_user_streaks_table',
            '2025_04_07_230513_create_exercise_completion_records_table',
            '2025_04_08_004336_add_features_fields_to_site_settings_table',
            '2025_04_08_005123_add_more_content_fields_to_site_settings_table',
            '2025_04_08_014435_add_csrf_protection_to_forms',
            '2025_04_08_015804_add_missing_fields_to_site_settings_table',
            '2025_04_09_000845_create_store_items_table',
            '2025_04_09_001021_create_user_inventories_table',
            '2025_04_09_001343_create_user_avatars_table',
            '2025_04_09_011536_create_email_notifications_table',
        ];
        
        // Verificar si existen en la tabla de migrations, y si no, agregarlas
        foreach ($problematicMigrations as $migration) {
            $exists = DB::table('migrations')->where('migration', $migration)->exists();
            
            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => 1
                ]);
            }
        }
        
        // Modificar tabla usuarios para añadir last_login_at si no existe
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hacemos nada aquí para evitar pérdida de datos
    }
};
