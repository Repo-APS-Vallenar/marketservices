<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ServiceProvider; // Necesario para asociar/desasociar perfiles de proveedor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Para encriptar contraseñas
use Illuminate\Validation\Rule; // Para reglas de validación

class UserController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden acceder a este controlador
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Acceso no autorizado. Solo los administradores pueden gestionar usuarios.');
            }
            return $next($request);
        })->except(['profile', 'updateProfile']); // Excluir métodos de perfil personal
    }

    /**
     * Muestra una lista de todos los usuarios.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::all();
        $roles = ['customer', 'service_provider', 'admin']; // Definir los roles aquí
        return view('admin.users.index', compact('users', 'roles')); // Pasar roles a la vista
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Puedes pasar roles si quieres que el admin seleccione el rol al crear
        $roles = ['customer', 'service_provider', 'admin'];
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Almacena un nuevo usuario en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', 'string', Rule::in(['customer', 'service_provider', 'admin'])],
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        // Si el rol es 'service_provider', crea también un perfil de ServiceProvider asociado
        if ($user->role === 'service_provider') {
            ServiceProvider::create(['user_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $roles = ['customer', 'service_provider', 'admin'];
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Actualiza un usuario existente en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['customer', 'service_provider', 'admin'])],
        ];

        // Solo validar la contraseña si se proporciona
        if ($request->filled('password')) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $validatedData = $request->validate($rules);

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->role = $validatedData['role'];

        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->save();

        // Lógica para manejar la creación/eliminación del perfil de ServiceProvider
        if ($user->role === 'service_provider' && !$user->serviceProvider) {
            ServiceProvider::create(['user_id' => $user->id]);
        } elseif ($user->role !== 'service_provider' && $user->serviceProvider) {
            $user->serviceProvider->delete(); // Elimina el perfil de proveedor si el rol cambia
        }

        return redirect()->route('admin.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Elimina un usuario de la base de datos.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // No permitir eliminar al propio administrador
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // Eliminar el perfil de ServiceProvider asociado si existe
        if ($user->serviceProvider) {
            $user->serviceProvider->delete();
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
