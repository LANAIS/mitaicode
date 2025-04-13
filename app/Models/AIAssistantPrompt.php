<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIAssistantPrompt extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ai_assistant_prompts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'prompt_template',
        'description',
        'parameters',
        'example_outputs',
        'category',
        'difficulty_level',
        'created_by',
        'is_active',
        'is_system',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'parameters' => 'array',
        'example_outputs' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Get the user that created this prompt.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    /**
     * Scope a query to only include idea generator prompts.
     */
    public function scopeIdeaGenerator($query)
    {
        return $query->where('type', 'idea_generator');
    }

    /**
     * Scope a query to only include exercise variant prompts.
     */
    public function scopeExerciseVariant($query)
    {
        return $query->where('type', 'exercise_variant');
    }

    /**
     * Scope a query to only include quality checker prompts.
     */
    public function scopeQualityChecker($query)
    {
        return $query->where('type', 'quality_checker');
    }

    /**
     * Scope a query to only include prompts of a specific category.
     */
    public function scopeOfCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include prompts of a specific difficulty level.
     */
    public function scopeOfDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    /**
     * Format prompt with parameters.
     */
    public function formatPrompt($params = [])
    {
        $formattedPrompt = $this->prompt_template;
        
        foreach ($params as $key => $value) {
            $formattedPrompt = str_replace('{{' . $key . '}}', $value, $formattedPrompt);
        }
        
        return $formattedPrompt;
    }
}
