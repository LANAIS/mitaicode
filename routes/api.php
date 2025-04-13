<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PythonExecutionController;
use App\Http\Controllers\Api\AITutorController;
use App\Http\Controllers\Api\AvatarPreviewController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Ruta para ejecutar código Python
    Route::post('/execute-python', function (Request $request) {
        // Implementa la lógica para ejecutar código Python aquí
    });
    
    // Ruta para el Tutor IA
    Route::post('/ai-tutor/ask', [AITutorController::class, 'ask']);
    
    // Rutas para el avatar
    Route::get('/avatar/preview', [AvatarPreviewController::class, 'preview']);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('users', UserController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('groups', GroupController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('posts', PostController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('comments', CommentController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('likes', LikeController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('follows', FollowController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('messages', MessageController::class);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware')
])->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});
