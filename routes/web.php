<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClassroomController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\HackathonController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\HackathonChatController;
use App\Http\Controllers\HackathonDeliverableController;
use App\Http\Controllers\HackathonRoundController;
use App\Http\Controllers\HackathonRoundsController;
use App\Http\Controllers\Student\HackathonController as StudentHackathonController;
use App\Http\Controllers\PromptLessonController;
use App\Http\Controllers\PromptExerciseController;
use App\Http\Controllers\PromptDeliverableController;
use App\Http\Controllers\TeachingChallengeController;
use App\Http\Controllers\ChallengeExerciseController;
use App\Http\Controllers\StudentChallengeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\UserAchievementController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteSettingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeacherHackathonController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Teacher\AIAssistantController;
use App\Http\Controllers\Admin\AnalyticsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirección especial para /hackathons
Route::get('/hackathons', function() {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    if (Auth::user()->role === 'student') {
        return redirect()->route('student.hackathons.index');
    }
    
    return redirect()->route('hackathons.index');
})->name('hackathons.redirect');

// Página de inicio (landing page)
Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

// Ruta alternativa para forzar recarga de configuración
Route::get('/refresh', function() {
    // Limpiar cachés
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Cache::flush();
    
    // Redirigir a la página principal
    return redirect('/');
});

// Ruta para debug de settings (temporal)
Route::get('/debug-settings', function() {
    $settings = \App\Models\SiteSettings::first();
    echo "Valores actuales en la base de datos:<br>";
    echo "- ID: " . $settings->id . "<br>";
    echo "- current_students: " . $settings->current_students . "<br>";
    echo "- goal_students_target: " . $settings->goal_students_target . "<br>";
    echo "- goal_subtitle: " . $settings->goal_subtitle . "<br>";
    echo "<hr>";
    
    // Información sobre la conexión a la BD
    echo "Conexión a la base de datos: " . DB::connection()->getDatabaseName() . "<br>";
    echo "PDO attributes:<br>";
    $pdo = DB::connection()->getPdo();
    echo "- ATTR_EMULATE_PREPARES: " . ($pdo->getAttribute(\PDO::ATTR_EMULATE_PREPARES) ? 'true' : 'false') . "<br>";
    echo "- ATTR_ERRMODE: " . $pdo->getAttribute(\PDO::ATTR_ERRMODE) . "<br>";
    
    // Limpiar cachés
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "Cachés limpiadas.<br>";
    
    return;
});

// Ruta para mostrar el contenido de la tabla site_settings directamente
Route::get('/db-settings', function() {
    // Consulta directa a la base de datos
    $settings = \Illuminate\Support\Facades\DB::table('site_settings')->first();
    
    if (!$settings) {
        return ['error' => 'No se encontró ninguna configuración en la base de datos.'];
    }
    
    // Convertir a array para mostrar
    return [
        'id' => $settings->id,
        'goal_title' => $settings->goal_title,
        'goal_subtitle' => $settings->goal_subtitle, 
        'current_students' => $settings->current_students,
        'goal_students_target' => $settings->goal_students_target,
        'current_projects' => $settings->current_projects,
        'current_badges' => $settings->current_badges,
        'created_at' => $settings->created_at,
        'updated_at' => $settings->updated_at,
    ];
});

// Ruta para hacer consulta SQL directa
Route::get('/raw-sql', function() {
    try {
        // Conexión directa a la base de datos
        $pdo = new \PDO(
            'mysql:host='.env('DB_HOST').';dbname='.env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );
        
        // Consulta SQL directa
        $stmt = $pdo->query('SELECT id, goal_title, goal_subtitle, current_students, goal_students_target, current_projects, current_badges, created_at, updated_at FROM site_settings LIMIT 1');
        
        // Obtener el resultado
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$row) {
            return ['error' => 'No se encontró ninguna configuración en la base de datos.'];
        }
        
        return $row;
    } catch (\Exception $e) {
        return [
            'error' => 'Error al conectar con la base de datos: ' . $e->getMessage(),
            'connection' => [
                'host' => env('DB_HOST'),
                'database' => env('DB_DATABASE'),
                'username' => env('DB_USERNAME')
            ]
        ];
    }
});

// Ruta para actualizar directamente valores en la base de datos
Route::get('/update-db', function() {
    try {
        // Conexión directa a la base de datos
        $pdo = new \PDO(
            'mysql:host='.env('DB_HOST').';dbname='.env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );
        
        // Valores de prueba
        $currentStudents = 999;  // Valor muy distintivo para verificar
        $goalSubtitle = 'Actualizado directamente por SQL - ' . time();
        
        // Actualizar directamente con SQL
        $stmt = $pdo->prepare('UPDATE site_settings SET current_students = :students, goal_subtitle = :subtitle, updated_at = NOW() WHERE id = 1');
        $stmt->execute([
            ':students' => $currentStudents,
            ':subtitle' => $goalSubtitle
        ]);
        
        // Verificar si se actualizó alguna fila
        $rowCount = $stmt->rowCount();
        
        if ($rowCount > 0) {
            return [
                'success' => 'Se actualizaron ' . $rowCount . ' filas',
                'new_values' => [
                    'current_students' => $currentStudents,
                    'goal_subtitle' => $goalSubtitle
                ]
            ];
        } else {
            return ['warning' => 'No se actualizó ninguna fila. Es posible que no exista una configuración con ID 1.'];
        }
    } catch (\Exception $e) {
        return ['error' => 'Error al actualizar la base de datos: ' . $e->getMessage()];
    }
});

// Ruta para forzar el reseteo y recreación de los datos de configuración
Route::get('/reset-settings', function() {
    try {
        // Borrar configuración actual
        \App\Models\SiteSettings::query()->delete();
        
        // Crear nueva configuración con valores predeterminados
        $settings = \App\Models\SiteSettings::getSettings();
        
        // Obtener configuración actual para verificar
        $current = \App\Models\SiteSettings::first();
        
        // Limpiar todas las cachés
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Cache::flush();
        
        return [
            'success' => 'Configuración recreada con éxito',
            'id' => $settings->id,
            'current_id' => $current ? $current->id : null,
            'current_students' => $current ? $current->current_students : null,
        ];
    } catch (\Exception $e) {
        return ['error' => 'Error al resetear configuración: ' . $e->getMessage()];
    }
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas para usuarios autenticados
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile
    Route::prefix('profile')->group(function() {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::patch('/notification-preferences', [ProfileController::class, 'updateNotificationPreferences'])->name('profile.notification-preferences.update');
        Route::put('/password', [App\Http\Controllers\PasswordController::class, 'update'])->name('password.update');
    });

    // Verificación de email (ruta adicional)
    Route::post('/email/verification-notification', [ProfileController::class, 'sendVerification'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');
    
    // Ruta rápida para acceder al perfil del usuario autenticado
    Route::get('/my-profile', function() {
        return redirect()->route('users.show', ['user' => Auth::id()]);
    })->name('my.profile');
    
    // Rutas de usuarios
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // Rutas para profesores
    Route::middleware('role:teacher')->group(function () {
        Route::resource('classrooms', ClassroomController::class);
    });

    // Rutas para estudiantes
    Route::middleware('role:student')->group(function () {
        Route::get('/student-dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
        Route::resource('missions', MissionController::class);
        
        // Rutas para desafíos de estudiantes
        Route::prefix('student-challenges')->name('student.challenges.')->group(function () {
            Route::get('/', [StudentChallengeController::class, 'index'])->name('index');
            Route::get('/{id}', [StudentChallengeController::class, 'show'])->name('show');
            Route::get('/exercise/{exerciseId}', [StudentChallengeController::class, 'showExercise'])->name('exercise');
            Route::post('/exercise/{exerciseId}/submit', [StudentChallengeController::class, 'submit'])->name('submit');
            Route::get('/submission/{submissionId}', [StudentChallengeController::class, 'showSubmission'])->name('submission');
            Route::get('/exercise/result/{submissionId}', [StudentChallengeController::class, 'showResult'])->name('exercise.result');
        });
        
        // Rutas de la tienda virtual y avatar
        Route::prefix('store')->name('store.')->group(function () {
            Route::get('/', [App\Http\Controllers\StoreController::class, 'index'])->name('index');
            Route::get('/category/{category}', [App\Http\Controllers\StoreController::class, 'category'])->name('category');
            Route::get('/item/{slug}', [App\Http\Controllers\StoreController::class, 'show'])->name('show');
            Route::post('/purchase/{itemId}', [App\Http\Controllers\StoreController::class, 'purchase'])->name('purchase');
            Route::get('/inventory', [App\Http\Controllers\StoreController::class, 'inventory'])->name('inventory');
            Route::post('/equip/{inventoryId}', [App\Http\Controllers\StoreController::class, 'equip'])->name('equip');
            Route::post('/unequip/{inventoryId}', [App\Http\Controllers\StoreController::class, 'unequip'])->name('unequip');
            Route::get('/avatar', [App\Http\Controllers\StoreController::class, 'avatar'])->name('avatar');
            Route::post('/avatar', [App\Http\Controllers\StoreController::class, 'updateAvatar'])->name('avatar.update');
        });
        
        // Rutas para desafíos de programación
        Route::prefix('challenges')->group(function () {
            Route::get('/{type?}/{level?}', [ChallengeController::class, 'index'])->name('challenges.index');
            Route::post('/submit', [ChallengeController::class, 'submitCode'])->name('challenges.submit');
            Route::get('/certificate/{id}', [ChallengeController::class, 'getCertificate'])->name('challenges.certificate');
            Route::get('/certificate/download/{id}', [ChallengeController::class, 'downloadCertificate'])->name('certificates.download');
        });
    });

    // Rutas para hackathones para estudiantes con su propio middleware
    Route::middleware(['auth', 'role:student'])->group(function () {
        // Ruta principal de hackathones para estudiantes - definida explícitamente
        Route::get('/estudiantes/hackathons', [StudentHackathonController::class, 'index'])->name('student.hackathons.index');
        
        // Rutas generales de hackathones
        Route::prefix('estudiantes/hackathons')->name('student.hackathons.')->group(function () {
            // Redefinir la ruta show para asegurarnos que funcione
            Route::get('/{id}', [StudentHackathonController::class, 'show'])->name('details');
            Route::get('/{id}/join', [StudentHackathonController::class, 'joinForm'])->name('join');
            Route::post('/{id}/create-team', [StudentHackathonController::class, 'createTeam'])->name('create-team');
            Route::post('/{id}/join-team', [StudentHackathonController::class, 'joinTeam'])->name('join-team');
            Route::post('/{id}/leave', [StudentHackathonController::class, 'leaveTeam'])->name('leave');
        });
        
        // Rutas específicas para equipos
        Route::prefix('estudiantes/hackathons/team')->name('student.hackathons.')->group(function () {
            // Ruta principal para ver el equipo
            Route::get('/{id}', [StudentHackathonController::class, 'team'])->name('team');
            
            // Acciones de gestión del equipo
            Route::post('/{id}/invite', [StudentHackathonController::class, 'inviteTeamMember'])->name('invite');
            Route::post('/{id}/submit', [StudentHackathonController::class, 'submitDeliverable'])->name('submit-deliverable');
            Route::post('/{id}/transfer-leadership', [StudentHackathonController::class, 'transferLeadership'])->name('transfer-leadership');
            Route::put('/{id}', [StudentHackathonController::class, 'updateTeam'])->name('update-team');
            Route::post('/{id}/remove/{memberId}', [StudentHackathonController::class, 'removeMember'])->name('remove-member');
            Route::get('/{id}/certificate', [StudentHackathonController::class, 'downloadCertificate'])->name('certificate');
            
            // Características del equipo
            Route::get('/{id}/edit', [StudentHackathonController::class, 'edit'])->name('team.edit');
            Route::get('/{id}/repository', [StudentHackathonController::class, 'repository'])->name('team.repository');
            
            // Chat
            Route::get('/{id}/chat', [HackathonChatController::class, 'index'])->name('team.chat');
            Route::post('/{id}/chat/send', [HackathonChatController::class, 'sendMessage'])->name('team.chat.send');
            Route::get('/{id}/chat/messages', [HackathonChatController::class, 'getMessages'])->name('team.chat.messages');
            
            // Entregables
            Route::get('/{id}/deliverables', [HackathonDeliverableController::class, 'index'])->name('team.deliverables');
            Route::post('/{id}/deliverables/upload', [HackathonDeliverableController::class, 'upload'])->name('team.deliverables.upload');
        });
        
        // Otras rutas de hackathones (fuera del grupo de prefijo)
        Route::get('/estudiantes/hackathons/deliverable/{id}/download', [HackathonDeliverableController::class, 'download'])->name('student.hackathons.deliverable.download');
    });

    // Rutas para ambos roles
    Route::resource('badges', BadgeController::class)->only(['index', 'show']);
    Route::resource('projects', ProjectController::class);

    // Ruta para mostrar un hackathon específico
    Route::get('/hackathons/{id}', [HackathonController::class, 'show'])->name('hackathons.show');
    
    // Rutas para hackathones (resto de acciones)
    Route::resource('hackathons', HackathonController::class)->except(['show']);

    // Rutas de gamificación
    Route::middleware(['auth'])->prefix('gamification')->name('gamification.')->group(function () {
        // Rutas de leaderboard
        Route::get('/leaderboards', [LeaderboardController::class, 'index'])->name('leaderboards.index');
        Route::get('/leaderboards/{id}', [LeaderboardController::class, 'show'])->name('leaderboards.show');
        
        // Rutas de logros
        Route::get('/achievements', [UserAchievementController::class, 'index'])->name('achievements.index');
        Route::get('/achievements/{id}', [UserAchievementController::class, 'show'])->name('achievements.show');
        
        // Ruta para ver perfil de usuario con estadísticas
        Route::get('/stats', [GamificationController::class, 'userStats'])->name('stats');
    });

    // Rutas para lecciones de Prompt Engineering
    Route::middleware(['auth'])->group(function () {
        // Rutas para lecciones
        Route::get('/prompt-lessons', [PromptLessonController::class, 'index'])->name('prompt_lessons.index');
        Route::get('/prompt-lessons/create', [PromptLessonController::class, 'create'])->name('prompt_lessons.create');
        Route::post('/prompt-lessons', [PromptLessonController::class, 'store'])->name('prompt_lessons.store');
        Route::get('/prompt-lessons/{id}', [PromptLessonController::class, 'show'])->name('prompt_lessons.show');
        Route::get('/prompt-lessons/{id}/edit', [PromptLessonController::class, 'edit'])->name('prompt_lessons.edit');
        Route::put('/prompt-lessons/{id}', [PromptLessonController::class, 'update'])->name('prompt_lessons.update');
        Route::delete('/prompt-lessons/{id}', [PromptLessonController::class, 'destroy'])->name('prompt_lessons.destroy');
        Route::get('/prompt-lessons/{id}/statistics', [PromptLessonController::class, 'statistics'])->name('prompt_lessons.statistics');
        
        // Rutas para ejercicios
        Route::get('/prompt-exercises/{id}', [PromptExerciseController::class, 'show'])->name('prompt_exercises.show');
        Route::post('/prompt-exercises/{id}/test', [PromptExerciseController::class, 'testPrompt'])->name('prompt_exercises.test');
        Route::post('/prompt-exercises/{id}/submit', [PromptExerciseController::class, 'submitPrompt'])->name('prompt_exercises.submit');
        
        // Rutas para entregables
        Route::get('/prompt-lessons/{lessonId}/deliverables', [PromptDeliverableController::class, 'index'])->name('prompt_deliverables.index');
        Route::get('/prompt-lessons/{lessonId}/deliverables/student/{studentId}', [PromptDeliverableController::class, 'studentDeliverables'])->name('prompt_deliverables.student');
        Route::get('/prompt-lessons/{lessonId}/my-deliverables', [PromptDeliverableController::class, 'myDeliverables'])->name('prompt_deliverables.my_deliverables');
        Route::get('/prompt-lessons/{lessonId}/deliverables/create', [PromptDeliverableController::class, 'create'])->name('prompt_deliverables.create');
        Route::post('/prompt-lessons/{lessonId}/deliverables', [PromptDeliverableController::class, 'store'])->name('prompt_deliverables.store');
        Route::get('/prompt-deliverables/{id}', [PromptDeliverableController::class, 'show'])->name('prompt_deliverables.show');
        Route::get('/prompt-deliverables/{id}/edit', [PromptDeliverableController::class, 'edit'])->name('prompt_deliverables.edit');
        Route::put('/prompt-deliverables/{id}', [PromptDeliverableController::class, 'update'])->name('prompt_deliverables.update');
        Route::delete('/prompt-deliverables/{id}', [PromptDeliverableController::class, 'destroy'])->name('prompt_deliverables.destroy');
        Route::get('/prompt-deliverables/{id}/grade', [PromptDeliverableController::class, 'grade'])->name('prompt_deliverables.grade');
        Route::post('/prompt-deliverables/{id}/grade', [PromptDeliverableController::class, 'submitGrade'])->name('prompt_deliverables.submit_grade');
        Route::get('/prompt-deliverables/{id}/download', [PromptDeliverableController::class, 'download'])->name('prompt_deliverables.download');

        // Rutas para desafíos de enseñanza
        Route::prefix('teaching-challenges')->middleware('role:teacher,admin')->group(function () {
            Route::get('/', [TeachingChallengeController::class, 'index'])->name('challenges.index');
            Route::get('/create', [TeachingChallengeController::class, 'create'])->name('challenges.create');
            Route::post('/', [TeachingChallengeController::class, 'store'])->name('challenges.store');
            Route::get('/{id}', [TeachingChallengeController::class, 'show'])->name('challenges.show');
            Route::get('/{id}/edit', [TeachingChallengeController::class, 'edit'])->name('challenges.edit');
            Route::put('/{id}', [TeachingChallengeController::class, 'update'])->name('challenges.update');
            Route::delete('/{id}', [TeachingChallengeController::class, 'destroy'])->name('challenges.destroy');
            
            // Cambio de estado y vista previa
            Route::put('/{id}/status', [TeachingChallengeController::class, 'changeStatus'])->name('challenges.status');
            Route::get('/{id}/preview', [TeachingChallengeController::class, 'preview'])->name('challenges.preview');
            
            // Analíticas
            Route::get('/{id}/analytics', [TeachingChallengeController::class, 'analytics'])->name('challenges.analytics');
            Route::post('/{id}/analytics/update', [TeachingChallengeController::class, 'updateAnalytics'])->name('challenges.analytics.update');
            
            // Desafíos por clase
            Route::get('/class/{classId}', [TeachingChallengeController::class, 'indexForClass'])->name('challenges.class.index');
            
            // Ejercicios del desafío
            Route::get('/{challengeId}/exercises/create', [ChallengeExerciseController::class, 'create'])->name('challenges.exercises.create');
            Route::post('/{challengeId}/exercises', [ChallengeExerciseController::class, 'store'])->name('challenges.exercises.store');
            Route::get('/exercises/{id}/edit', [ChallengeExerciseController::class, 'edit'])->name('challenges.exercises.edit');
            Route::put('/exercises/{id}', [ChallengeExerciseController::class, 'update'])->name('challenges.exercises.update');
            Route::delete('/exercises/{id}', [ChallengeExerciseController::class, 'destroy'])->name('challenges.exercises.destroy');
            Route::post('/exercises/{id}/test', [ChallengeExerciseController::class, 'testExercise'])->name('challenges.exercises.test');
            Route::put('/exercises/reorder', [ChallengeExerciseController::class, 'reorder'])->name('challenges.exercises.reorder');
            
            // Rutas para calificar ejercicios
            Route::get('/exercises/{id}/submissions', [ChallengeExerciseController::class, 'showSubmissions'])->name('challenges.exercises.submissions');
            Route::get('/submissions/{id}/grade', [ChallengeExerciseController::class, 'showGradeForm'])->name('challenges.submissions.grade');
            Route::post('/submissions/{id}/grade', [ChallengeExerciseController::class, 'gradeSubmission'])->name('challenges.submissions.submit_grade');
        });
    });
});

// Rutas públicas para ver proyectos (sólo index y show)
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

// Rutas de Chat de Equipo en Hackathones
Route::middleware(['auth'])->group(function () {
    // Estas rutas ya están definidas en el grupo de student.hackathons
    // Eliminar estas definiciones duplicadas
    /* 
    Route::get('/hackathons/team/{teamId}/chat', [HackathonChatController::class, 'index'])->name('student.hackathons.team.chat');
    Route::post('/hackathons/team/{teamId}/chat', [HackathonChatController::class, 'sendMessage'])->name('student.hackathons.team.chat.send');
    Route::get('/hackathons/team/{teamId}/chat/messages', [HackathonChatController::class, 'getMessages'])->name('student.hackathons.team.chat.messages');

    // Rutas de Entregables de Equipo en Hackathones
    Route::get('/hackathons/team/{teamId}/deliverables', [HackathonDeliverableController::class, 'index'])->name('student.hackathons.team.deliverables');
    Route::post('/hackathons/team/{teamId}/deliverables', [HackathonDeliverableController::class, 'upload'])->name('student.hackathons.team.deliverables.upload');
    Route::get('/hackathons/deliverable/{deliverableId}/download', [HackathonDeliverableController::class, 'download'])->name('student.hackathons.deliverable.download');
    */
});

// Rutas para profesores y administradores
Route::middleware(['auth', 'role:teacher,admin'])->group(function () {
    // Listado de hackathones donde el profesor participa como creador o juez
    Route::get('/teacher/hackathons', [HackathonController::class, 'teacherIndex'])->name('teacher.hackathons');
    
    // Evaluación de entregables de hackathon
    Route::get('/hackathons/{hackathonId}/deliverables', [HackathonDeliverableController::class, 'evaluateIndex'])->name('hackathons.deliverables.evaluate');
    Route::get('/hackathons/{hackathonId}/deliverables/{roundId}', [HackathonDeliverableController::class, 'evaluateIndex'])->name('hackathons.deliverables.evaluate.round');
    Route::post('/hackathons/deliverable/{deliverableId}/evaluate', [HackathonDeliverableController::class, 'saveEvaluation'])->name('hackathons.deliverable.evaluate');
    Route::get('/hackathons/deliverable/{deliverableId}/download', [HackathonDeliverableController::class, 'download'])->name('hackathons.deliverable.download');
    
    // Rutas para gestionar las rondas de los hackathons
    Route::get('hackathons/{hackathonId}/rounds', [HackathonRoundsController::class, 'index'])->name('hackathons.rounds.index');
    Route::get('hackathons/{hackathonId}/rounds/create', [HackathonRoundsController::class, 'create'])->name('hackathons.rounds.create');
    Route::post('hackathons/{hackathonId}/rounds', [HackathonRoundsController::class, 'store'])->name('hackathons.rounds.store');
    Route::get('hackathons/{hackathonId}/rounds/{roundId}/edit', [HackathonRoundsController::class, 'edit'])->name('hackathons.rounds.edit');
    Route::put('hackathons/{hackathonId}/rounds/{roundId}', [HackathonRoundsController::class, 'update'])->name('hackathons.rounds.update');
    Route::delete('hackathons/{hackathonId}/rounds/{roundId}', [HackathonRoundsController::class, 'destroy'])->name('hackathons.rounds.destroy');
    Route::put('hackathons/{hackathonId}/rounds/{roundId}/status', [HackathonRoundsController::class, 'updateStatus'])->name('hackathons.rounds.status');
    
    // Rutas para gestionar jurados
    Route::get('hackathons/{id}/judges', [HackathonController::class, 'judges'])->name('hackathons.judges');
    Route::post('hackathons/{id}/judges', [HackathonController::class, 'updateJudges'])->name('hackathons.judges.update');
    Route::delete('hackathons/{id}/judges/{userId}', [HackathonController::class, 'removeJudge'])->name('hackathons.judges.destroy');
    
    // Rutas para gestionar estado de inscripciones y avanzar fases
    Route::put('hackathons/{id}/toggle-registration', [HackathonController::class, 'toggleRegistration'])->name('hackathons.toggle.registration');
    Route::put('hackathons/{id}/advance-round', [HackathonController::class, 'advanceRound'])->name('hackathons.advance.round');
    Route::put('hackathons/{id}/update-status', [HackathonController::class, 'updateStatus'])->name('hackathons.update.status');
});

// Rutas del admin
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    // Configuración del sitio
    Route::get('/site-settings', [App\Http\Controllers\SiteSettingsController::class, 'edit'])
        ->name('admin.site-settings.edit');
    
    Route::post('/site-settings', [App\Http\Controllers\SiteSettingsController::class, 'update'])
        ->name('admin.site-settings.update');
    
    // Gestión de Items de la Tienda
    Route::resource('store-items', App\Http\Controllers\Admin\StoreItemController::class)
        ->names([
            'index' => 'admin.store-items.index',
            'create' => 'admin.store-items.create',
            'store' => 'admin.store-items.store',
            'edit' => 'admin.store-items.edit',
            'update' => 'admin.store-items.update',
            'destroy' => 'admin.store-items.destroy'
        ]);
        
    // Rutas de Analítica
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/analytics/users', [AnalyticsController::class, 'users'])->name('admin.analytics.users');
    Route::get('/analytics/content', [AnalyticsController::class, 'content'])->name('admin.analytics.content');
    Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('admin.analytics.export');
    Route::get('/analytics/refresh', [AnalyticsController::class, 'refreshAll'])->name('admin.analytics.refresh');
    Route::get('/analytics/refresh-purchases', [AnalyticsController::class, 'refreshPurchases'])->name('admin.analytics.refresh-purchases');
    
    // Gestión de Notificaciones por Email
    Route::resource('notifications', App\Http\Controllers\Admin\NotificationController::class)
        ->names([
            'index' => 'admin.notifications.index',
            'create' => 'admin.notifications.create',
            'store' => 'admin.notifications.store',
            'edit' => 'admin.notifications.edit',
            'update' => 'admin.notifications.update',
            'destroy' => 'admin.notifications.destroy'
        ]);
        
    Route::get('/notifications/{notification}/test', [App\Http\Controllers\Admin\NotificationController::class, 'test'])
        ->name('admin.notifications.test');
        
    Route::get('/notifications/{notification}/stats', [App\Http\Controllers\Admin\NotificationController::class, 'stats'])
        ->name('admin.notifications.stats');
});

// Ruta para diagnóstico completo
Route::get('/diagnostico', function() {
    $result = [];
    
    // 1. Información de conexión
    $result['conexion'] = [
        'db_host' => env('DB_HOST'),
        'db_database' => env('DB_DATABASE'),
        'db_connection' => env('DB_CONNECTION'),
        'app_url' => env('APP_URL'),
        'server_port' => $_SERVER['SERVER_PORT'] ?? 'No disponible',
        'request_scheme' => $_SERVER['REQUEST_SCHEME'] ?? 'No disponible',
        'http_host' => $_SERVER['HTTP_HOST'] ?? 'No disponible',
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'No disponible',
    ];
    
    // 2. Verificar tabla
    try {
        $pdo = new \PDO(
            'mysql:host='.env('DB_HOST').';dbname='.env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );
        
        // Verificar si la tabla existe
        $stmt = $pdo->query("SHOW TABLES LIKE 'site_settings'");
        $result['tabla_existe'] = $stmt->rowCount() > 0;
        
        if ($result['tabla_existe']) {
            // Contar registros
            $stmt = $pdo->query("SELECT COUNT(*) FROM site_settings");
            $result['cantidad_registros'] = $stmt->fetchColumn();
            
            // Obtener datos actuales
            $stmt = $pdo->query("SELECT id, current_students, goal_students_target, goal_subtitle FROM site_settings LIMIT 1");
            $result['datos_actuales'] = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Probar actualización directa
            $updateStmt = $pdo->prepare("UPDATE site_settings SET current_students = :students WHERE id = :id");
            $updateStmt->execute([
                ':students' => 777, // Valor distintivo para verificar
                ':id' => $result['datos_actuales']['id'] ?? 1
            ]);
            $result['filas_actualizadas'] = $updateStmt->rowCount();
            
            // Verificar actualización
            $stmt = $pdo->query("SELECT id, current_students, goal_students_target, goal_subtitle FROM site_settings LIMIT 1");
            $result['datos_despues_update'] = $stmt->fetch(\PDO::FETCH_ASSOC);
        }
        
        // Verificar modelo
        $modelData = \App\Models\SiteSettings::first();
        $result['datos_modelo'] = $modelData ? [
            'id' => $modelData->id,
            'current_students' => $modelData->current_students,
            'goal_subtitle' => $modelData->goal_subtitle
        ] : null;
        
    } catch (\Exception $e) {
        $result['error_db'] = $e->getMessage();
    }
    
    return $result;
});

// Ruta para ver las tablas en la base de datos
Route::get('/show-tables', function() {
    try {
        $pdo = new \PDO(
            'mysql:host='.env('DB_HOST').';dbname='.env('DB_DATABASE'),
            env('DB_USERNAME'),
            env('DB_PASSWORD')
        );
        
        // Mostrar todas las tablas
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $result = ['tablas' => $tables];
        
        // Mostrar la estructura de la tabla site_settings
        if (in_array('site_settings', $tables)) {
            $stmt = $pdo->query("DESCRIBE site_settings");
            $structure = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $result['estructura_site_settings'] = $structure;
            
            // Mostrar el contenido actual de site_settings
            $stmt = $pdo->query("SELECT * FROM site_settings LIMIT 1");
            $data = $stmt->fetch(\PDO::FETCH_ASSOC);
            $result['datos_site_settings'] = $data;
        }
        
        return $result;
    } catch (\Exception $e) {
        return ['error' => 'Error: ' . $e->getMessage()];
    }
});

// Rutas para que los profesores vean y administren hackathons
Route::get('/crear-hackathon', [TeacherHackathonController::class, 'create'])->middleware(['auth', 'role:teacher,admin'])->name('teacher.hackathon.create');
Route::post('/crear-hackathon', [TeacherHackathonController::class, 'store'])->middleware(['auth', 'role:teacher,admin'])->name('teacher.hackathon.store');
Route::get('/editar-hackathon/{hackathon_id}', [TeacherHackathonController::class, 'edit'])->middleware(['auth', 'role:teacher,admin'])->name('teacher.hackathon.edit');
Route::put('/editar-hackathon/{hackathon_id}', [TeacherHackathonController::class, 'update'])->middleware(['auth', 'role:teacher,admin'])->name('teacher.hackathon.update');
Route::get('/editar-hackathon/{hackathon_id}/rounds', [TeacherHackathonController::class, 'redirectToRounds'])->middleware(['auth', 'role:teacher,admin'])->name('teacher.hackathon.rounds');

// Ruta para debugging - Ver todas las rutas disponibles
Route::get('/debug-routes', function() {
    $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function ($route) {
        return [
            'method' => implode('|', $route->methods()),
            'uri' => $route->uri(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    })->filter(function ($route) {
        // Filtramos para mostrar solo las rutas relacionadas con hackathons
        return str_contains($route['name'] ?? '', 'hackathon') || 
               str_contains($route['uri'] ?? '', 'hackathon');
    })->values();
    
    return view('debug.routes', ['routes' => $routes]);
});

// Ruta temporal para verificar la estructura de la tabla
Route::get('/debug-table-structure', function() {
    // Obtenemos la estructura detallada usando SQL directo
    $teamUserColumns = DB::select("SHOW COLUMNS FROM hackathon_team_user");
    $teamsColumns = DB::select("SHOW COLUMNS FROM hackathon_teams");
    
    // Obtenemos las claves foráneas
    $foreignKeys = DB::select("
        SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME IN ('hackathon_team_user', 'hackathon_teams') 
        AND REFERENCED_TABLE_NAME IS NOT NULL
    ");
    
    return [
        'hackathon_team_user_columns' => $teamUserColumns,
        'hackathon_teams_columns' => $teamsColumns,
        'foreign_keys' => $foreignKeys
    ];
});

// Rutas para el Asistente de IA para profesores
Route::prefix('teacher/ai-assistant')->middleware(['auth', 'verified'])->name('teacher.ai_assistant.')->group(function () {
    Route::get('/', [AIAssistantController::class, 'index'])->name('index');
    
    // Generador de ideas
    Route::get('/idea-generator', [AIAssistantController::class, 'showIdeaGenerator'])->name('idea_generator');
    Route::post('/generate-idea', [AIAssistantController::class, 'generateIdeas'])->name('generate_idea');
    
    // Generador de variantes
    Route::get('/variant-generator', [AIAssistantController::class, 'showVariantGenerator'])->name('variant_generator');
    Route::get('/exercises/{challengeId}', [AIAssistantController::class, 'getExercises'])->name('get_exercises');
    Route::post('/generate-variant', [AIAssistantController::class, 'generateVariants'])->name('generate_variant');
    
    // Generador de estructura
    Route::get('/structure-generator', [AIAssistantController::class, 'showStructureGenerator'])->name('structure_generator');
    Route::post('/generate-structure', [AIAssistantController::class, 'generateStructure'])->name('generate_structure');
    
    // Verificador de calidad
    Route::get('/quality-checker', [AIAssistantController::class, 'showQualityChecker'])->name('quality_checker');
    Route::post('/check-quality', [AIAssistantController::class, 'checkQuality'])->name('check_quality');
    
    // Configuración de prompts (solo admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('/prompts', [AIAssistantController::class, 'showPrompts'])->name('prompts');
        Route::post('/prompts', [AIAssistantController::class, 'storePrompt'])->name('store_prompt');
        Route::put('/prompts/{id}', [AIAssistantController::class, 'updatePrompt'])->name('update_prompt');
        Route::delete('/prompts/{id}', [AIAssistantController::class, 'deletePrompt'])->name('delete_prompt');
    });
});
