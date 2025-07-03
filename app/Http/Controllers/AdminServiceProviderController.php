<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use App\Models\User; // Para eager loading de los datos del usuario asociado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminServiceProviderController extends Controller
{
    public function __construct()
    {
        // Solo administradores pueden acceder a este controlador
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'admin') {
                abort(403, 'Acceso no autorizado. Solo los administradores pueden gestionar proveedores de servicios.');
            }
            return $next($request);
        });
    }

    /**
     * Muestra una lista de los proveedores de servicios pendientes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Cargamos los proveedores con estado 'pending' y eager load los datos de su usuario
        $pendingProviders = ServiceProvider::where('status', 'pending')
                                           ->with('user') // Carga el modelo User relacionado
                                           ->get();
                                           
        // También podríamos cargar 'categories' si el proveedor ya las ha asociado y queremos verlas
        $pendingProviders = ServiceProvider::where('status', 'pending')
                                           ->with('user', 'categories')
                                           ->get();

        return view('admin.pending-providers.index', compact('pendingProviders'));
    }

    /**
     * Muestra los detalles de un proveedor de servicios específico (para el modal de detalles).
     *
     * @param  \App\Models\ServiceProvider  $serviceProvider
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ServiceProvider $serviceProvider)
    {
        // Eager load las relaciones necesarias para el modal de detalles
        $serviceProvider->load('user', 'categories', 'services'); // Puedes añadir más si es necesario
        return response()->json($serviceProvider);
    }

    /**
     * Aprueba un proveedor de servicios.
     *
     * @param  \App\Models\ServiceProvider  $serviceProvider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(ServiceProvider $serviceProvider)
    {
        // Si el proveedor ya no está pendiente, podría ser un error o doble click
        if ($serviceProvider->status !== 'pending') {
            return redirect()->back()->with('error', 'El proveedor ya ha sido procesado.');
        }

        $serviceProvider->status = 'approved';
        $serviceProvider->save();

        return redirect()->route('admin.pending-providers.index')->with('success', 'Proveedor de servicios aprobado exitosamente.');
    }

    /**
     * Rechaza un proveedor de servicios.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceProvider  $serviceProvider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, ServiceProvider $serviceProvider)
    {
        // Opcional: validar que se proporcione un motivo de rechazo
        $request->validate(['rejection_reason' => 'required|string|min:10']);

        if ($serviceProvider->status !== 'pending') {
            return redirect()->back()->with('error', 'El proveedor ya ha sido procesado.');
        }

        $serviceProvider->status = 'rejected';
        // $serviceProvider->rejection_reason = $request->input('rejection_reason'); // Si añades un campo de razón
        $serviceProvider->save();

        // Puedes enviar una notificación al usuario si lo deseas
        // $serviceProvider->user->notify(new ProviderRejectedNotification($request->input('rejection_reason')));

        return redirect()->route('admin.pending-providers.index')->with('success', 'Proveedor de servicios rechazado.');
    }
}
