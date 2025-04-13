<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'level',
        'points',
        'image_path',
        'is_active',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exercises()
    {
        return $this->hasMany(ChallengeExercise::class);
    }

    public function progress()
    {
        return $this->hasMany(ChallengeProgress::class);
    }

    public function studentProgress()
    {
        return $this->hasMany(ChallengeStudentProgress::class);
    }

    public function analytics()
    {
        return $this->hasMany(ChallengeAnalytic::class);
    }
} 