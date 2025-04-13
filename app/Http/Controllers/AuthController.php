<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    /**
     * Mostrar el formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Manejar una solicitud de inicio de sesión.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Actualizar última fecha de inicio de sesión
            $user = Auth::user();
            $user->last_login_at = now();
            $user->save();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar la sesión del usuario.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Mostrar el formulario de registro.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Manejar una solicitud de registro.
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'role' => ['required', 'in:student,teacher'],
        ]);

        // Crear nuevo usuario
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => $request->role,
            'date_registered' => now(),
            'is_active' => true,
        ]);

        // Crear perfil según el rol
        if ($request->role === 'student') {
            StudentProfile::create([
                'user_id' => $user->user_id,
                'level' => 1,
                'xp_points' => 0,
                'total_blocks_used' => 0,
                'total_missions_completed' => 0,
                'parent_email' => $request->parent_email ?? null,
                'age' => $request->age ?? null,
            ]);
        } else {
            TeacherProfile::create([
                'user_id' => $user->user_id,
                'institution' => $request->institution ?? null,
                'bio' => $request->bio ?? null,
                'website' => $request->website ?? null,
            ]);
        }

        // Iniciar sesión con el nuevo usuario
        Auth::login($user);
        
        // Disparar evento de registro de usuario para enviar notificación de bienvenida
        event(new \App\Events\UserRegistered($user));

        return redirect()->route('dashboard');
    }
}
