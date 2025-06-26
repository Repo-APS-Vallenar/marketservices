<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking; // Asegúrate de importar tu modelo Booking
use App\Models\Service; // Asegúrate de importar tu modelo Service
use Illuminate\Support\Facades\Auth; // Para obtener el ID del usuario autenticado
use Carbon\Carbon; // Para trabajar con fechas y horas (opcional, pero útil para validación)
use Illuminate\Pagination\LengthAwarePaginator; // Importa esta clase para crear un paginador vacío

class BookingController extends Controller
{
    /**
     * Display a listing of the bookings.
     * Muestra una lista de las reservas.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $perPage = 10;
        $currentPage = request()->get('page', 1);

        // Inicializa $bookings como un paginador vacío por defecto
        // Esto garantiza que siempre sea un objeto paginador, incluso si no hay datos.
        $bookings = new LengthAwarePaginator(
            [], // Array de ítems (vacío al inicio)
            0,  // Total de ítems (0 al inicio)
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()] // Opciones para la URL del paginador
        );
        
        if (Auth::check()) {
            // Usando la propiedad 'role' directamente
            if (Auth::user()->role === 'customer') {
                $bookings = Booking::with(['service.serviceProvider.user', 'customer'])
                    ->where('customer_id', Auth::id())
                    ->orderByDesc('scheduled_at')
                    ->paginate($perPage);
            } elseif (Auth::user()->role === 'service_provider') {
                // Si el usuario autenticado es un proveedor de servicios
                $bookings = Booking::with(['customer', 'service'])
                    ->whereHas('service', function($q) {
                        // Busca servicios asociados a este proveedor de servicios autenticado
                        // Asumiendo que serviceProvider tiene un user_id que coincide con Auth::id()
                        // o que ServiceProvider tiene una relación user y accedemos a su id.
                        // Si ServiceProvider tiene un campo user_id y este es Auth::id(), usa:
                        // $q->where('service_provider_id', Auth::user()->serviceProvider->id);
                        // Asegúrate de que Auth::user()->serviceProvider exista y tenga un id.
                        // Para evitar un posible error, si Auth::user()->serviceProvider no está disponible,
                        // esta rama podría no devolver resultados o requerir una lógica diferente.
                        // Si Auth::user() es directamente el serviceProvider (raro), se usaría Auth::id()
                        $q->where('service_provider_id', Auth::user()->serviceProvider->id ?? null);
                    })
                    ->orderByDesc('scheduled_at')
                    ->paginate($perPage);
            } elseif (Auth::user()->role === 'admin') {
                // Si el usuario autenticado es un administrador, muestra todas las reservas
                $bookings = Booking::with(['customer', 'service.serviceProvider.user'])
                    ->orderByDesc('scheduled_at')
                    ->paginate($perPage);
            }
        } 
        
        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show the form for creating a new booking.
     * Muestra el formulario para crear una nueva reserva.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('bookings.create');
    }

    /**
     * Store a newly created booking in storage.
     * Almacena una nueva reserva creada en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validar los datos de entrada del formulario
        $validatedData = $request->validate([
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => [
                'required',
                'date',
                'after_or_equal:' . Carbon::now()->format('Y-m-d H:i:s'),
            ],
            'notes' => 'nullable|string|max:1000',
        ]);

        // Asegúrate de que el usuario esté autenticado y tenga un rol permitido para reservar
        if (!Auth::check() || (Auth::user()->role !== 'customer' && Auth::user()->role !== 'admin')) {
            return back()->withErrors(['auth' => 'Debes iniciar sesión como cliente o administrador para realizar una reserva.']);
        }

        // 2. Obtener el servicio para extraer información necesaria (proveedor, precio)
        $service = Service::findOrFail($validatedData['service_id']);

        // 3. Crear una nueva instancia de Booking y asignar los valores
        $booking = new Booking();
        
        $booking->service_id = $validatedData['service_id'];
        $booking->customer_id = Auth::id(); // El ID del usuario autenticado es el customer_id
        $booking->service_provider_id = $service->service_provider_id;
        $booking->scheduled_at = $validatedData['scheduled_at'];
        $booking->notes = $validatedData['notes'];

        // Asignar valores por defecto para los campos restantes
        $booking->status = 'pending';
        $booking->total_price = $service->price;
        $booking->payment_status = 'pending';
        $booking->ended_at = null;

        // 4. Guardar la reserva en la base de datos
        $booking->save();

        // 5. Redirigir al usuario con un mensaje de éxito
        return redirect()->route('bookings.index')->with('success', '¡Reserva creada con éxito!');
    }

    /**
     * Display the specified booking.
     * Muestra una reserva específica.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $booking = Booking::with(['customer', 'service', 'serviceProvider.user'])->findOrFail($id);

        // Opcional: Asegúrate de que el usuario autenticado tenga permiso para ver esta reserva
        if (Auth::id() !== $booking->customer_id && Auth::id() !== ($booking->service->serviceProvider->user_id ?? null) && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return view('bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     * Muestra el formulario para editar la reserva especificada.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $booking = Booking::with(['service'])->findOrFail($id);
        
        // Opcional: Asegúrate de que solo el propietario o un administrador pueda editar
        if (Auth::id() !== $booking->customer_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $services = Service::all(); 

        return view('bookings.edit', compact('booking', 'services'));
    }

    /**
     * Update the specified booking in storage.
     * Actualiza la reserva especificada en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validatedData = $request->validate([
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after_or_equal:now',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $service = Service::findOrFail($validatedData['service_id']);

        $booking->service_id = $validatedData['service_id'];
        $booking->scheduled_at = $validatedData['scheduled_at'];
        $booking->notes = $validatedData['notes'];
        $booking->status = $validatedData['status'];
        $booking->service_provider_id = $service->service_provider_id;
        $booking->total_price = $service->price;

        $booking->save();

        return redirect()->route('bookings.index')->with('success', 'Reserva actualizada con éxito!');
    }

    /**
     * Remove the specified booking from storage.
     * Elimina la reserva especificada de la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        // Opcional: Asegúrate de que solo el propietario o un administrador pueda eliminar
        if (Auth::id() !== $booking->customer_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $booking->delete();

        return redirect()->route('bookings.index')->with('success', 'Reserva eliminada con éxito.');
    }
}
