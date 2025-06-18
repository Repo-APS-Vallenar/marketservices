<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $bookings = $user->bookings()->with(['serviceProvider.user', 'service'])->latest()->get();
        return response()->json(['user' => $user, 'bookings' => $bookings]);
    }

    public function createBooking(Request $request)
    {
        $request->validate([
            'service_provider_id' => 'required|exists:service_providers,id',
            'service_id' => 'required|exists:services,id',
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $booking = Booking::create([
            'customer_id' => Auth::id(),
            'service_provider_id' => $request->service_provider_id,
            'service_id' => $request->service_id,
            'scheduled_at' => $request->scheduled_at,
            'notes' => $request->notes,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return response()->json(['message' => 'Reserva creada exitosamente', 'booking' => $booking], 201);
    }

    public function cancelBooking(Booking $booking)
    {
        if (Auth::id() !== $booking->customer_id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($booking->status === 'pending' || $booking->status === 'confirmed') {
            $booking->status = 'cancelled';
            $booking->save();
            return response()->json(['message' => 'Reserva cancelada']);
        }

        return response()->json(['message' => 'La reserva no puede ser cancelada en su estado actual'], 400);
    }

    public function addReview(Request $request, Booking $booking)
    {
        if (Auth::id() !== $booking->customer_id || $booking->status !== 'completed') {
            return response()->json(['message' => 'No autorizado o la reserva no está completada'], 403);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::create([
            'customer_id' => Auth::id(),
            'service_provider_id' => $booking->service_provider_id,
            'booking_id' => $booking->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $serviceProvider = $booking->serviceProvider;
        $serviceProvider->review_count++;
        $serviceProvider->rating = ($serviceProvider->rating * ($serviceProvider->review_count - 1) + $request->rating) / $serviceProvider->review_count;
        $serviceProvider->save();

        return response()->json(['message' => 'Reseña agregada exitosamente', 'review' => $review], 201);
    }
} 