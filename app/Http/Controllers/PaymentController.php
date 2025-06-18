<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    public function processPayment(Request $request, Booking $booking)
    {
        if (Auth::id() !== $booking->customer_id) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($booking->payment_status === 'paid') {
            return response()->json(['message' => 'La reserva ya ha sido pagada'], 400);
        }

        $request->validate([
            'payment_method_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $request->amount * 100,
                'currency' => 'usd',
                'payment_method' => $request->payment_method_id,
                'confirmation_method' => 'manual',
                'confirm' => true,
                'description' => 'Pago por reserva #' . $booking->id,
            ]);

            $payment = Payment::create([
                'booking_id' => $booking->id,
                'customer_id' => Auth::id(),
                'amount' => $request->amount,
                'currency' => 'USD',
                'payment_method' => 'Stripe',
                'transaction_id' => $paymentIntent->id,
                'status' => $paymentIntent->status === 'succeeded' ? 'completed' : 'pending',
            ]);

            if ($paymentIntent->status === 'succeeded') {
                $booking->payment_status = 'paid';
                $booking->save();
                return response()->json(['message' => 'Pago procesado exitosamente', 'payment' => $payment], 200);
            } else {
                return response()->json(['message' => 'El pago no se pudo completar', 'status' => $paymentIntent->status], 400);
            }

        } catch (\Stripe\Exception\CardException $e) {
            return response()->json(['message' => $e->getError()->message], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al procesar el pago: ' . $e->getMessage()], 500);
        }
    }
} 