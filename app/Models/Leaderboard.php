<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Leaderboard extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'reference_id',
        'start_date',
        'end_date',
        'is_active',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Obtener las entradas asociadas a esta tabla de puntajes.
     */
    public function entries(): HasMany
    {
        return $this->hasMany(LeaderboardEntry::class);
    }

    /**
     * Crear una tabla de puntajes semanal.
     *
     * @param string $name Nombre de la tabla
     * @param int|null $referenceId ID de referencia (por ejemplo, ID de un desafío específico)
     * @return self
     */
    public static function createWeekly(string $name, ?int $referenceId = null): self
    {
        $now = Carbon::now();
        $startOfWeek = $now->copy()->startOfWeek();
        $endOfWeek = $now->copy()->endOfWeek();

        return self::create([
            'name' => $name,
            'type' => 'weekly',
            'reference_id' => $referenceId,
            'start_date' => $startOfWeek,
            'end_date' => $endOfWeek,
            'is_active' => true,
        ]);
    }

    /**
     * Crear una tabla de puntajes mensual.
     *
     * @param string $name Nombre de la tabla
     * @param int|null $referenceId ID de referencia (por ejemplo, ID de un desafío específico)
     * @return self
     */
    public static function createMonthly(string $name, ?int $referenceId = null): self
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        return self::create([
            'name' => $name,
            'type' => 'monthly',
            'reference_id' => $referenceId,
            'start_date' => $startOfMonth,
            'end_date' => $endOfMonth,
            'is_active' => true,
        ]);
    }

    /**
     * Obtener las mejores entradas de la tabla de puntajes.
     *
     * @param int $limit Número máximo de entradas a retornar
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTopEntries(int $limit = 10)
    {
        return $this->entries()
            ->orderBy('score', 'desc')
            ->with('user')
            ->take($limit)
            ->get();
    }

    /**
     * Actualizar las posiciones de todas las entradas en el ranking.
     */
    public function updateRankings(): void
    {
        $entries = $this->entries()
            ->orderBy('score', 'desc')
            ->get();
        
        $position = 1;
        foreach ($entries as $entry) {
            $entry->position = $position++;
            $entry->save();
        }
    }

    /**
     * Crear o actualizar un registro en la tabla de puntajes para un usuario.
     *
     * @param int $userId ID del usuario
     * @param int $points Puntaje a asignar
     * @param array $additionalData Datos adicionales a actualizar (streak, completed_challenges, etc.)
     * @return LeaderboardEntry
     */
    public function updateUserScore(int $userId, int $points, array $additionalData = []): LeaderboardEntry
    {
        $entry = $this->entries()->firstOrNew(['user_id' => $userId]);
        $entry->score = $points;
        
        foreach ($additionalData as $key => $value) {
            if (in_array($key, ['streak', 'completed_challenges', 'completed_exercises'])) {
                $entry->{$key} = $value;
            }
        }
        
        $entry->save();
        
        // Actualizar todas las posiciones
        $this->updateRankings();
        
        return $entry;
    }
}
