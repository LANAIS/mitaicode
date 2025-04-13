<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::where('user_id', $id)->firstOrFail();
        
        // Determinar si el usuario actual está viendo su propio perfil
        $isOwnProfile = Auth::check() && Auth::id() == $user->user_id;
        
        return view('users.show', compact('user', 'isOwnProfile'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    
    /**
     * Show the user profile page.
     */
    public function profile()
    {
        $user = Auth::user();
        
        return view('users.profile', compact('user'));
    }
    
    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->user_id, 'user_id'),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);
        
        // Actualizar los datos del usuario
        User::where('user_id', $user->user_id)
            ->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
            ]);
        
        return redirect()->route('profile')->with('success', 'Perfil actualizado correctamente');
    }
}
