<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceProvider;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceProviderController extends Controller
{
    public function dashboard()
    {
        $serviceProvider = Auth::user()->serviceProvider;
        if (!$serviceProvider) {
            abort(403, 'Proveedor de servicios no encontrado');
        }

        $pendingCount = $serviceProvider->bookings()->where('status', 'pending')->count();
        $upcomingCount = $serviceProvider->bookings()->where('status', 'confirmed')->where('scheduled_at', '>=', now())->count();
        $completedCount = $serviceProvider->bookings()->where('status', 'completed')->count();
        $upcomingBookings = $serviceProvider->bookings()
            ->with(['customer', 'service'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return view('provider.dashboard', compact('pendingCount', 'upcomingCount', 'completedCount', 'upcomingBookings'));
    }

    public function updateProfile(Request $request)
    {
        $serviceProvider = Auth::user()->serviceProvider;
        if (!$serviceProvider) {
            return response()->json(['message' => 'Proveedor de servicios no encontrado'], 404);
        }

        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
        ]);

        $serviceProvider->update($request->all());

        return response()->json(['message' => 'Perfil actualizado exitosamente', 'serviceProvider' => $serviceProvider]);
    }

    public function addService(Request $request)
    {
        $serviceProvider = Auth::user()->serviceProvider;
        if (!$serviceProvider) {
            return response()->json(['message' => 'Proveedor de servicios no encontrado'], 404);
        }

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|max:50',
            'estimated_duration' => 'nullable|integer|min:1',
        ]);

        $service = $serviceProvider->services()->create($request->all());

        return response()->json(['message' => 'Servicio agregado exitosamente', 'service' => $service], 201);
    }

    public function updateService(Request $request, Service $service)
    {
        $serviceProvider = Auth::user()->serviceProvider;
        if (!$serviceProvider || $service->service_provider_id !== $serviceProvider->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'category_id' => 'exists:categories,id',
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'price_unit' => 'nullable|string|max:50',
            'estimated_duration' => 'nullable|integer|min:1',
        ]);

        $service->update($request->all());

        return response()->json(['message' => 'Servicio actualizado exitosamente', 'service' => $service]);
    }

    public function deleteService(Service $service)
    {
        $serviceProvider = Auth::user()->serviceProvider;
        if (!$serviceProvider || $service->service_provider_id !== $serviceProvider->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $service->delete();

        return response()->json(['message' => 'Servicio eliminado exitosamente']);
    }

    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $serviceProvider = Auth::user()->serviceProvider;
        if (!$serviceProvider || $booking->service_provider_id !== $serviceProvider->id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $booking->status = $request->status;
        $booking->save();

        return response()->json(['message' => 'Estado de reserva actualizado', 'booking' => $booking]);
    }
} 