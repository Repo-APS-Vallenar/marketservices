<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Necesario para Auth::routes() y Auth::user()
use App\Http\Controllers\HomeController; // Ejemplo: Si tienes una HomeController para la página de inicio
use App\Http\Controllers\ClientController; // Para tu marketplace de servicios
use App\Http\Controllers\BookingController; // Para la gestión de reservas de clientes/proveedores
use App\Http\Controllers\UserController; // Para la gestión de usuarios por el admin
use App\Http\Controllers\DashboardController; // Para el dashboard general (si tienes uno)
use App\Http\Controllers\ProfileController; // Importado para tus rutas de perfil
use App\Http\Controllers\ServiceController; // Importado para gestión de servicios de proveedor
use App\Http\Controllers\MessageController; // Importado para mensajes
use App\Http\Controllers\ServicesMarketplaceController; // Importado para marketplace de servicios
use App\Http\Controllers\AdminController; // Importado para el AdminController
use App\Http\Controllers\CategoryController; // Importado para la gestión de categorías


// Ruta de la página de inicio
Route::get('/', function () {
    return view('welcome'); // O el nombre de tu vista de inicio principal
})->name('home');

// Rutas de autenticación generadas por Laravel (login, register, reset password, etc.)
// Estas rutas son proporcionadas por el scaffolding de Laravel Breeze/Jetstream o ui.
// Auth::routes(); // Comentado porque se maneja con require __DIR__.'/auth.php';

// Ruta del Marketplace (accesible para todos, incluso no autenticados)
Route::get('/marketplace', [ServicesMarketplaceController::class, 'index'])->name('marketplace.services.index');
Route::get('/marketplace/services/{service}', [ServicesMarketplaceController::class, 'show'])->name('marketplace.services.show');


// =========================================================================
// RUTAS PARA USUARIOS AUTENTICADOS (Middleware 'auth')
// =========================================================================

Route::middleware(['auth'])->group(function () {

    // Ruta del Dashboard general (accesible para todos los roles autenticados)
    // Este dashboard puede redirigir o mostrar contenido diferente según el rol.
    Route::get('/dashboard', function () {
        $user = Auth::user();
        if ($user && $user->role === 'service_provider') {
            // Asumiendo que ServiceProviderController tiene un método dashboard
            return app(\App\Http\Controllers\ServiceProviderController::class)->dashboard();
        }
        // Si no es proveedor o no tiene un dashboard específico, va al dashboard genérico
        return view('dashboard');
    })->name('dashboard'); // Ya tienes un dashboard definido así, lo mantengo.

    // Rutas para el Perfil de Usuario (común para todos los roles autenticados)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // Rutas para gestión de servicios (proveedores)
    // Asumiendo que ServiceController es para que el proveedor gestione SUS servicios
    Route::resource('services', ServiceController::class); // Esto crea las rutas CRUD: services.index, services.create, etc.

    // Rutas para la gestión de RESERVAS (Accesibles por clientes, administradores y quizás proveedores)
    // Si BookingController se usa para el panel de usuarios, está bien aquí.
    Route::resource('bookings', BookingController::class); // Esto crea las rutas CRUD: bookings.index, bookings.create, etc.

    // Rutas para "Mensajes" (entre clientes y proveedores)
    Route::resource('messages', MessageController::class); // Esto crea las rutas CRUD: messages.index, messages.create, etc.


    // =========================================================================
    // RUTAS PARA EL PANEL DE ADMINISTRACIÓN (Middleware 'admin' o verificación en constructor)
    // =========================================================================

    // Agrupamos rutas de administración bajo un prefijo 'admin' y un nombre 'admin.'
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard del administrador
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Gestión de usuarios
        // Eliminado: Route::get('/users', [AdminController::class, 'listUsers'])->name('users.index'); // CONFLICTIVA con UserController
        // Este bloque ahora maneja todas las rutas CRUD de usuarios con UserController
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
        // Si necesitas una ruta específica para actualizar el rol de un usuario desde AdminController, aquí está:
        Route::put('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update.role');


        // Gestión de proveedores (ej. proveedores pendientes de aprobación)
        Route::get('/providers/pending', [AdminController::class, 'listPendingProviders'])->name('providers.pending');
        Route::post('/providers/{serviceProvider}/verify', [AdminController::class, 'verifyServiceProvider'])->name('providers.verify');

        // Rutas para la gestión de CATEGORÍAS (Category Management)
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/', [CategoryController::class, 'store'])->name('store');
            Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        // Gestión de reservas (vista del admin)
        Route::get('/bookings', [AdminController::class, 'listBookings'])->name('bookings.index');
    });
});



// =========================================================================
// RUTAS DE AUTENTICACIÓN ADICIONALES (Generalmente de Breeze/Jetstream)
// =========================================================================
// Este archivo requiere las rutas de autenticación de Laravel Breeze/Jetstream
// Si no usas Breeze/Jetstream, puedes comentar esta línea y usar Auth::routes()
require __DIR__ . '/auth.php';

// Eliminado: Auth::routes(); // ¡DUPLICADO!
// Eliminado: Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home'); // DUPLICADO si ya tienes '/' o '/dashboard'
