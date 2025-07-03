<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\MessageController; // Importado para mensajes
use App\Http\Controllers\ServicesMarketplaceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminServiceProviderController;
use App\Http\Controllers\ServiceProviderProfileController;


// Ruta de la página de inicio
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rutas de autenticación
require __DIR__.'/auth.php';

// Ruta del Marketplace (accesible para todos, incluso no autenticados)
Route::get('/marketplace', [ServicesMarketplaceController::class, 'index'])->name('marketplace.services.index');
Route::get('/marketplace/services/{service}', [ServicesMarketplaceController::class, 'show'])->name('marketplace.services.show');


// =========================================================================
// RUTAS PARA USUARIOS AUTENTICADOS (Middleware 'auth')
// =========================================================================

Route::middleware(['auth'])->group(function () {

    // Ruta del Dashboard general
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user && $user->role === 'service_provider') {
            return app(\App\Http\Controllers\ServiceProviderController::class)->dashboard();
        }
        return view('dashboard');
    })->name('dashboard');

    // Rutas para el Perfil de Usuario (común para todos los roles autenticados)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Rutas para gestión de servicios (proveedores)
    Route::resource('services', ServiceController::class);

    // Rutas para la gestión de RESERVAS
    Route::resource('bookings', BookingController::class);

    // Rutas para "Mensajes"
    // Excluimos 'show' y 'create' del resource para definirlos de forma personalizada
    Route::resource('messages', MessageController::class)->except(['show', 'create']);

    // NUEVA RUTA: Para mostrar una conversación específica con un usuario
    // Ahora espera un modelo User ($partner)
    Route::get('/messages/conversation/{partner}', [MessageController::class, 'showConversation'])->name('messages.show_conversation');

    // NUEVA RUTA: Para el formulario de creación de un nuevo mensaje (si lo necesitas)
    // Se puede pasar opcionalmente el user_id del destinatario
    Route::get('/messages/create/{user_id?}', [MessageController::class, 'create'])->name('messages.create');


    // =========================================================================
    // RUTAS ESPECÍFICAS PARA EL ROL 'service_provider'
    // =========================================================================
    Route::prefix('provider')->name('provider.')->group(function () {
        Route::get('/profile', [ServiceProviderProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ServiceProviderProfileController::class, 'update'])->name('profile.update');
    });


    // =========================================================================
    // RUTAS PARA EL PANEL DE ADMINISTRACIÓN
    // =========================================================================

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
        Route::put('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update.role');

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('pending-providers')->name('pending-providers.')->group(function () {
            Route::get('/', [AdminServiceProviderController::class, 'index'])->name('index');
            Route::get('/{serviceProvider}', [AdminServiceProviderController::class, 'show'])->name('show');
            Route::put('/{serviceProvider}/approve', [AdminServiceProviderController::class, 'approve'])->name('approve');
            Route::put('/{serviceProvider}/reject', [AdminServiceProviderController::class, 'reject'])->name('reject');
        });

        Route::get('/bookings', [AdminController::class, 'listBookings'])->name('bookings.index');
    });
});
