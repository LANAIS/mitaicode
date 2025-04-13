<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     */
    public function index(Request $request)
    {
        $query = EmailNotification::query();
        
        // Filtrar por tipo si se especifica
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Store a newly created notification in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'audience' => 'required|string|in:all,students,teachers',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
            'show_once' => 'boolean',
        ]);
        
        $notification = new EmailNotification();
        $notification->name = $validated['title'];
        $notification->type = $validated['type'];
        $notification->subject = $validated['subject'];
        $notification->content = $validated['message'];
        $notification->trigger_event = 'manual'; // Por defecto, el evento es manual
        $notification->audience = $validated['audience'];
        $notification->expires_at = $validated['expires_at'] ?? null;
        $notification->is_active = isset($validated['active']);
        $notification->show_once = isset($validated['show_once']);
        $notification->send_time = now()->format('H:i:s');
        $notification->created_by = Auth::id();
        
        $notification->save();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notificación creada correctamente');
    }

    /**
     * Show the form for editing the specified notification.
     */
    public function edit($id)
    {
        $notification = EmailNotification::findOrFail($id);
        return view('admin.notifications.edit', compact('notification'));
    }

    /**
     * Update the specified notification in storage.
     */
    public function update(Request $request, $id)
    {
        $notification = EmailNotification::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:50',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'audience' => 'required|string|in:all,students,teachers',
            'expires_at' => 'nullable|date',
            'active' => 'boolean',
            'show_once' => 'boolean',
        ]);
        
        $notification->name = $validated['title'];
        $notification->type = $validated['type'];
        $notification->subject = $validated['subject'];
        $notification->content = $validated['message'];
        $notification->audience = $validated['audience'];
        $notification->expires_at = $validated['expires_at'] ?? null;
        $notification->is_active = isset($validated['active']);
        $notification->show_once = isset($validated['show_once']);
        
        $notification->save();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notificación actualizada correctamente');
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy($id)
    {
        $notification = EmailNotification::findOrFail($id);
        $notification->delete();
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notificación eliminada correctamente');
    }
    
    /**
     * Send a test email of the notification.
     */
    public function test($id)
    {
        $notification = EmailNotification::findOrFail($id);
        
        // Aquí iría el código para enviar un email de prueba al usuario actual
        // Por ahora solo redireccionamos con un mensaje
        
        return redirect()->route('admin.notifications.index')
            ->with('success', 'Email de prueba enviado a tu dirección de correo');
    }
    
    /**
     * Show statistics for a specific notification.
     */
    public function stats($id)
    {
        $notification = EmailNotification::findOrFail($id);
        
        // Simulación de estadísticas (en un caso real, vendrían de la base de datos)
        $stats = (object)[
            'total_sent' => rand(100, 1000),
            'successful_sent' => rand(90, 950),
            'total_opened' => rand(50, 800),
            'total_clicked' => rand(20, 400),
        ];
        
        // Simulación de logs diarios para el gráfico
        $dailyStats = collect();
        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $sent = rand(5, 50);
            $opened = rand(2, $sent - 1);
            $clicked = rand(1, $opened - 1);
            
            $dailyStats->push([
                'date' => $date,
                'total_sent' => $sent,
                'total_opened' => $opened,
                'total_clicked' => $clicked,
            ]);
        }
        
        // Simulación de logs de envío (en un caso real, vendrían de la base de datos)
        $logs = User::inRandomOrder()->take(20)->get()->map(function($user) use ($notification) {
            return (object)[
                'user_id' => $user->id, // Usamos el id directamente
                'email' => $user->email,
                'sent' => rand(0, 10) > 1, // 90% de probabilidad de éxito
                'opened' => rand(0, 10) > 3, // 70% de probabilidad de apertura
                'clicked' => rand(0, 10) > 6, // 40% de probabilidad de clic
                'created_at' => now()->subDays(rand(0, 14))->subHours(rand(0, 23)),
            ];
        });
        
        // Convertir la colección a una instancia de LengthAwarePaginator
        $logs = new \Illuminate\Pagination\LengthAwarePaginator(
            $logs, // items
            count($logs), // total
            10, // por página
            request()->get('page', 1), // página actual
            ['path' => request()->url()] // opciones
        );
        
        return view('admin.notifications.stats', compact('notification', 'stats', 'dailyStats', 'logs'));
    }
} 