<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function listUsers()
    {
        $users = User::with('serviceProvider')->get();
        return response()->json($users);
    }

    public function updateUserRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:customer,service_provider,admin',
        ]);
        $user->role = $request->role;
        $user->save();
        return response()->json(['message' => 'Rol actualizado exitosamente', 'user' => $user]);
    }

    public function verifyServiceProvider(ServiceProvider $serviceProvider)
    {
        $serviceProvider->is_verified = true;
        $serviceProvider->save();
        return response()->json(['message' => 'Proveedor verificado exitosamente', 'serviceProvider' => $serviceProvider]);
    }
} 