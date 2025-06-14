<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChapterController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\NovelController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/password/reset', [AuthController::class, 'resetPassword']);
Route::post('/auth/password/update', [AuthController::class, 'updatePassword']);
Route::get('/novels', [NovelController::class, 'show']);
Route::get('/novels/{id}', [NovelController::class, 'showById']);
Route::get('/categories', [CategoryController::class, 'show']);
Route::controller(ChapterController::class)->group(function () {
    Route::get('/chapters/{novelId}', 'show');
    Route::get('/novels/{novelId}/chapters/{id}', 'showSingle');
});
// Ruta para probar configuración de Cloudinary
Route::get('/test-cloudinary-config', function () {
    return config('cloudinary');
});

// Rutas protegidas (todos los usuarios autenticados)
Route::middleware('auth:sanctum')->group(function () {
    // Rutas accesibles para todos los usuarios autenticados
    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'user'); // Prioridad a la versión personalizada
    });



    // Gestionar favoritos
    Route::controller(FavoriteController::class)->group(function () {
        Route::get('/favorites', 'show');                           // Favoritos del usuario autenticado
        Route::get('/users/{userId}/favorites', 'showByUserId');    // Favoritos de un usuario específico
        Route::post('/novels/{novelId}/favorites', 'store');
        Route::delete('/favorites/{id}', 'destroy');
    });

    // Cerrar sesión y cambiar contraseña (accesible para todos los autenticados)
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/password/change', [AuthController::class, 'changePassword']);

    // Rutas para Moderadores y Admins (role_id: 2 o 3)
    Route::middleware('moderatorOrAdmin')->group(function () {
        // Gestionar categorías
        Route::controller(CategoryController::class)->group(function () {
            Route::post('/categories/create', 'store');
            Route::patch('/categories/update/{id}', 'update');
            Route::delete('/categories/delete/{id}', 'destroy');
        });

        // Gestionar roles
        Route::controller(RoleController::class)->group(function () {
            Route::get('/roles', 'show');
            Route::post('/roles/create', 'store');
            Route::patch('/roles/update/{id}', 'update');
            Route::delete('/roles/delete/{id}', 'destroy');
        });

        // Gestionar novelas (crear, actualizar, eliminar)
        Route::controller(NovelController::class)->group(function () {
            Route::post('/novels/create', 'store');
            Route::patch('/novels/update/{id}', 'update');
            Route::delete('/novels/delete/{id}', 'destroy');
        });

        // Gestionar capítulos (crear, actualizar, eliminar)
        Route::controller(ChapterController::class)->group(function () {
            Route::post('/novels/{novelId}/chapters/create', 'store');
            Route::patch('/novels/{novelId}/chapters/update/{chapterNumber}', 'update');
            Route::delete('/novels/{novelId}/chapters/delete/{chapterNumber}', 'destroy');
        });
    });

    // Rutas solo para Admins (role_id: 3)
    Route::middleware('admin')->group(function () {
        // Gestionar usuarios
        Route::controller(UserController::class)->group(function () {
            Route::get('/users', 'show');
            Route::post('/users/create', 'store');
            Route::patch('/users/update/{id}', 'update');
            Route::delete('/users/delete/{id}', 'destroy');
        });
    });
});
