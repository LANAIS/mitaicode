<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAvatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvatarPreviewController extends Controller
{
    /**
     * Generar una previsualización del avatar basada en los parámetros dados
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function preview(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
        
        // Crear un avatar temporal para la previsualización
        $previewAvatar = new UserAvatar([
            'user_id' => $user->user_id,
            'base_avatar' => $request->input('base_avatar', 'default'),
            'skin_color' => $request->input('skin_color', '#F5D0A9'),
            'hair_style' => $request->input('hair_style', 'default'),
            'hair_color' => $request->input('hair_color', '#6F4E37'),
            'eye_type' => $request->input('eye_type', 'default'),
            'eye_color' => $request->input('eye_color', '#6F4E37'),
            'mouth_type' => $request->input('mouth_type', 'default'),
            'outfit' => $request->input('outfit', 'default'),
            'accessory' => $request->input('accessory'),
            'background' => $request->input('background', 'default'),
            'frame' => $request->input('frame'),
        ]);
        
        // Generar la imagen de previsualización
        try {
            $avatarUrl = $previewAvatar->generatePreviewAvatar();
            
            return response()->json([
                'success' => true,
                'avatar_url' => $avatarUrl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al generar la previsualización',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 