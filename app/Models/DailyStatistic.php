<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStatistic extends Model
{
    use HasFactory;

    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'daily_statistics';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'metric_type',
        'metric_value',
        'count'
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'count' => 'integer',
    ];

    /**
     * Obtener estadísticas por tipo de métrica y rango de fechas
     *
     * @param string $metricType
     * @param string $startDate
     * @param string $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getStatsByDateRange($metricType, $startDate, $endDate)
    {
        return self::where('metric_type', $metricType)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }

    /**
     * Incrementar o crear una estadística para una fecha específica
     *
     * @param string $date
     * @param string $metricType
     * @param string|null $metricValue
     * @param int $count
     * @return \App\Models\DailyStatistic
     */
    public static function incrementStat($date, $metricType, $metricValue = null, $count = 1)
    {
        $stat = self::firstOrNew([
            'date' => $date,
            'metric_type' => $metricType,
            'metric_value' => $metricValue,
        ]);

        $stat->count += $count;
        $stat->save();

        return $stat;
    }
}
