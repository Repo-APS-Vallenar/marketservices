<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Category;
use App\Models\Booking;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Estadísticas generales
        $stats = [
            'total_users' => User::count(),
            'total_providers' => User::where('role', 'service_provider')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_services' => Service::count(),
            'total_bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'completed_bookings' => Booking::where('status', 'completed')->count(),
            'total_categories' => Category::count(),
        ];

        // Últimos usuarios registrados
        $latest_users = User::latest()->take(5)->get();

        // Últimas reservas
        $latest_bookings = Booking::with(['customer', 'service', 'serviceProvider.user'])
                                ->latest()
                                ->take(5)
                                ->get();

        // Proveedores pendientes de verificación
        $unverified_providers = ServiceProvider::where('is_verified', false)
                                            ->with('user')
                                            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'latest_users',
            'latest_bookings',
            'unverified_providers'
        ));
    }

    public function listUsers()
    {
        $users = User::with('serviceProvider')->get();
        return view('admin.users.index', compact('users'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:customer,service_provider,admin',
        ]);
        
        $oldRole = $user->role;
        $user->role = $request->role;
        
        // Si el usuario se convierte en proveedor, crear registro de ServiceProvider
        if ($request->role === 'service_provider' && !$user->serviceProvider) {
            ServiceProvider::create([
                'user_id' => $user->id,
            ]);
        }
        
        $user->save();
        
        return redirect()->back()->with('success', 'Rol actualizado exitosamente');
    }

    public function verifyServiceProvider(ServiceProvider $serviceProvider)
    {
        $serviceProvider->is_verified = true;
        $serviceProvider->verified_at = now();
        $serviceProvider->save();
        
        return redirect()->back()->with('success', 'Proveedor verificado exitosamente');
    }

    public function listCategories()
    {
        $categories = Category::withCount('services')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function createCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        Category::create($request->all());
        return redirect()->back()->with('success', 'Categoría creada exitosamente');
    }

    public function updateCategory(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'icon' => 'nullable|string|max:255',
            'description' => 'nullable|string'
        ]);

        $category->update($request->all());
        return redirect()->back()->with('success', 'Categoría actualizada exitosamente');
    }

    public function deleteCategory(Category $category)
    {
        // Verificar si hay servicios en esta categoría
        if ($category->services()->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar una categoría que tiene servicios asociados');
        }

        $category->delete();
        return redirect()->back()->with('success', 'Categoría eliminada exitosamente');
    }
}