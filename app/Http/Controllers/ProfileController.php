<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Models\UserNotificationPreference;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $preferences = $user->notificationPreferences;
        
        $challengeAnalytics = null;
        
        // Cargar analíticas de desafíos para profesores
        if ($user->role === 'teacher') {
            $challengeAnalytics = \App\Models\TeachingChallenge::where('teacher_id', $user->user_id)
                ->with('analytics')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        }
        
        return view('profile.edit', [
            'user' => $user,
            'preferences' => $preferences,
            'challengeAnalytics' => $challengeAnalytics,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Validar los datos básicos del usuario
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username,'.$user->user_id.',user_id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->user_id.',user_id'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);
        
        // Actualizar datos básicos del usuario
        $user->username = $validated['username'];
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        
        // Verificar si el email ha cambiado
        if ($request->email !== $user->email) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
        }
        
        // Procesar avatar si se ha subido
        if ($request->hasFile('avatar')) {
            // Eliminar avatar anterior si existe
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            
            // Guardar el nuevo avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $avatarPath;
        }
        
        $user->save();
        
        // Actualizar datos específicos según el rol
        if ($user->role === 'student') {
            $studentProfile = $user->studentProfile;
            
            // Validar datos específicos del estudiante
            $studentData = $request->validate([
                'age' => ['nullable', 'integer', 'min:1', 'max:120'],
            ]);
            
            // Actualizar o crear perfil de estudiante
            if ($studentProfile) {
                $studentProfile->age = $studentData['age'] ?? $studentProfile->age;
                $studentProfile->save();
            } else {
                StudentProfile::create([
                    'user_id' => $user->user_id,
                    'age' => $studentData['age'] ?? null,
                    'level' => 1,
                    'xp_points' => 0,
                    'total_missions_completed' => 0,
                ]);
            }
        } elseif ($user->role === 'teacher') {
            $teacherProfile = $user->teacherProfile;
            
            // Validar datos específicos del profesor
            $teacherData = $request->validate([
                'institution' => ['nullable', 'string', 'max:255'],
            ]);
            
            // Actualizar o crear perfil de profesor
            if ($teacherProfile) {
                $teacherProfile->institution = $teacherData['institution'] ?? $teacherProfile->institution;
                $teacherProfile->save();
            } else {
                TeacherProfile::create([
                    'user_id' => $user->user_id,
                    'institution' => $teacherData['institution'] ?? null,
                    'total_students' => 0,
                ]);
            }
        }
        
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        // Eliminar el avatar si existe
        if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's notification preferences.
     */
    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'receive_emails' => 'boolean',
            'receive_welcome_emails' => 'boolean',
            'receive_reminder_emails' => 'boolean',
            'receive_inactive_emails' => 'boolean',
            'receive_new_content_emails' => 'boolean',
            'receive_marketing_emails' => 'boolean',
        ]);
        
        // Convertir las casillas de verificación no marcadas en valores booleanos
        $preferences = [
            'user_id' => $request->user()->user_id,
            'receive_emails' => isset($validated['receive_emails']),
            'receive_welcome_emails' => isset($validated['receive_welcome_emails']),
            'receive_reminder_emails' => isset($validated['receive_reminder_emails']),
            'receive_inactive_emails' => isset($validated['receive_inactive_emails']),
            'receive_new_content_emails' => isset($validated['receive_new_content_emails']),
            'receive_marketing_emails' => isset($validated['receive_marketing_emails']),
        ];
        
        // Buscar preferencias existentes o crear nuevas
        $userPreferences = $request->user()->notificationPreferences()->first();
        
        if ($userPreferences) {
            $userPreferences->update($preferences);
        } else {
            $request->user()->notificationPreferences()->create($preferences);
        }
        
        return Redirect::route('profile.edit')->with('status', 'notification-preferences-updated');
    }

    /**
     * Send a new email verification notification.
     */
    public function sendVerification(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
} 