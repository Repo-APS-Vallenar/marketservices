<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        // Obtener las reservas del proveedor autenticado
        $bookings = \App\Models\Booking::with(['customer', 'service'])
            ->whereHas('service', function($q) {
                $q->where('service_provider_id', auth()->id());
            })
            ->orderByDesc('scheduled_at')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        return view('bookings.create');
    }

    public function store(Request $request)
    {
        // Lógica para guardar una reserva
        return redirect()->route('bookings.index');
    }

    public function show($id)
    {
        // Lógica para mostrar una reserva específica
        return view('bookings.show');
    }

    public function edit($id)
    {
        // Lógica para editar una reserva
        return view('bookings.edit');
    }

    public function update(Request $request, $id)
    {
        // Lógica para actualizar una reserva
        return redirect()->route('bookings.index');
    }

    public function destroy($id)
    {
        // Lógica para eliminar una reserva
        return redirect()->route('bookings.index');
    }
} 