<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HackathonTeam extends Model
{
    use HasFactory;

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
        'name',
        'description',
        'hackathon_id',
        'project_name',
        'project_description',
        'is_winner',
        'position'
    ];

    /**
     * Get the hackathon that owns the team.
     */
    public function hackathon(): BelongsTo
    {
        return $this->belongsTo(Hackathon::class, 'hackathon_id', 'id');
    }

    /**
     * Get the leader of the team.
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * The members that belong to the team.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'hackathon_team_user', 'team_id', 'user_id')
                    ->withPivot('is_leader')
                    ->withTimestamps();
    }

    /**
     * Get the messages for the team.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(HackathonMessage::class, 'team_id', 'id');
    }

    /**
     * Get the deliverables for the team.
     */
    public function deliverables(): HasMany
    {
        return $this->hasMany(HackathonDeliverable::class, 'team_id', 'id');
    }

    /**
     * Check if a user is a member of the team.
     *
     * @param int $userId
     * @return bool
     */
    public function isMember(int $userId): bool
    {
        return $this->members()->where('hackathon_team_user.user_id', $userId)->exists();
    }

    /**
     * Check if a user is the leader of the team.
     *
     * @param int $userId
     * @return bool
     */
    public function isLeader(int $userId): bool
    {
        return $this->members()->where('hackathon_team_user.user_id', $userId)->wherePivot('is_leader', true)->exists();
    }
} 