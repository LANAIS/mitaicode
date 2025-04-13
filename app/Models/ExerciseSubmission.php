<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseSubmission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'exercise_submissions';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'exercise_id',
        'student_id',
        'submitted_code',
        'submitted_prompt',
        'execution_output',
        'ai_response',
        'score',
        'feedback',
        'status',
        'attempt_number',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'integer',
        'attempt_number' => 'integer',
    ];

    /**
     * Get the exercise that this submission belongs to.
     */
    public function exercise()
    {
        return $this->belongsTo(ChallengeExercise::class, 'exercise_id', 'id');
    }

    /**
     * Get the student that made this submission.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'user_id');
    }

    /**
     * Get the associated challenge through the exercise.
     */
    public function challenge()
    {
        return $this->exercise ? $this->exercise->challenge : null;
    }

    /**
     * Check if the submission is graded.
     */
    public function getIsGradedAttribute()
    {
        return $this->status === 'graded';
    }

    /**
     * Check if the submission is rejected.
     */
    public function getIsRejectedAttribute()
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the success status based on score.
     */
    public function getSuccessStatusAttribute()
    {
        if (!$this->is_graded) {
            return 'pending';
        }
        
        if ($this->score <= 0) {
            return 'failed';
        }
        
        if ($this->score < 70) {
            return 'partial';
        }
        
        return 'success';
    }

    /**
     * Get the formatted code with line numbers.
     */
    public function getFormattedCodeAttribute()
    {
        if (empty($this->submitted_code)) {
            return '';
        }
        
        $lines = explode("\n", $this->submitted_code);
        $formatted = '';
        
        foreach ($lines as $i => $line) {
            $lineNum = $i + 1;
            $formatted .= "<span class='line-number'>{$lineNum}.</span> " . htmlspecialchars($line) . "\n";
        }
        
        return $formatted;
    }

    /**
     * Grade a submission automatically based on test cases.
     * Returns true if grading was successful.
     */
    public function autoGrade()
    {
        // Solo para ejercicios de Python
        if (!$this->exercise || empty($this->exercise->test_cases) || empty($this->submitted_code)) {
            return false;
        }
        
        $testCases = json_decode($this->exercise->test_cases, true);
        if (empty($testCases)) {
            return false;
        }
        
        // Lógica para ejecutar el código contra los casos de prueba
        // Esto es un placeholder - en un sistema real, habría una integración con un servicio
        // que ejecute el código de Python de forma segura
        
        $passedTests = 0;
        $totalTests = count($testCases);
        
        // Simulación de evaluación - en producción, esto llamaría a un servicio de ejecución de código
        foreach ($testCases as $testCase) {
            // Aquí iría la lógica real de ejecución
            // Por ahora simplemente simulamos algunos resultados
            $testPassed = mt_rand(0, 1) === 1; // Simulación simple
            if ($testPassed) {
                $passedTests++;
            }
        }
        
        // Calcula el puntaje basado en pruebas pasadas
        $score = $totalTests > 0 ? round(($passedTests / $totalTests) * 100) : 0;
        
        // Actualiza el registro
        $this->score = $score;
        $this->status = 'graded';
        $this->feedback = "Pasaste {$passedTests} de {$totalTests} pruebas.";
        $this->save();
        
        return true;
    }
}
