<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\User;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        // Depuración 1: Verifica si la solicitud llega a este método y qué datos contiene
        // dd($request->all());

        try {
            $request->validate([
                'recipient_id' => 'required|exists:users,id',
                'message_content' => 'required|string|max:1000',
            ]);
            // Depuración 2: Si llegas aquí, la validación pasó
            // dd('Validación exitosa');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Depuración 3: Si la validación falla, muestra los errores
            // dd($e->errors());
            // Re-lanza la excepción para que Laravel maneje la respuesta de error de validación
            return response()->json(['errors' => $e->errors()], 422);
        }

        if (Auth::id() == $request->recipient_id) {
            // Depuración 4: Si intentas enviarte un mensaje a ti mismo
            // dd('Intentando enviarse un mensaje a sí mismo');
            return response()->json(['error' => 'No puedes enviarte un mensaje a ti mismo.'], 400);
        }

        try {
            Message::create([
                'sender_id' => Auth::id(),
                'recipient_id' => $request->recipient_id,
                'message_content' => $request->message_content,
                'read_at' => null,
            ]);
            // Depuración 5: Si llegas aquí, el mensaje se creó en la base de datos
            // dd('Mensaje creado en la base de datos');

        } catch (\Exception $e) {
            // Depuración 6: Si hay un error al crear el mensaje en la base de datos
            // dd('Error al crear mensaje: ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un problema al enviar el mensaje.'], 500);
        }

        // Depuración 7: Si todo fue exitoso, esta es la respuesta final
        // dd('Mensaje enviado exitosamente, devolviendo JSON');
        return response()->json(['success' => 'Mensaje enviado exitosamente.'], 200);
    }

    public function index()
    {
        $user = Auth::user();
        $messages = Message::where('sender_id', $user->id)
                           ->orWhere('recipient_id', $user->id)
                           ->with(['sender', 'recipient'])
                           ->orderBy('created_at', 'desc')
                           ->get();

        $conversations = $messages->groupBy(function ($message) use ($user) {
            return $message->sender_id == $user->id ? $message->recipient_id : $message->sender_id;
        });

        $conversationPartners = User::whereIn('id', $conversations->keys())->get()->keyBy('id');

        return view('messages.index', compact('conversations', 'conversationPartners'));
    }

    public function showConversation(User $partner)
    {
        $user = Auth::user();

        $messages = Message::where(function ($query) use ($user, $partner) {
                                $query->where('sender_id', $user->id)
                                      ->where('recipient_id', $partner->id);
                            })->orWhere(function ($query) use ($user, $partner) {
                                $query->where('sender_id', $partner->id)
                                      ->where('recipient_id', $user->id);
                            })
                            ->with(['sender', 'recipient'])
                            ->orderBy('created_at', 'asc')
                            ->get();

        Message::where('recipient_id', $user->id)
               ->where('sender_id', $partner->id)
               ->whereNull('read_at')
               ->update(['read_at' => now()]);

        return view('messages.show', compact('messages', 'partner'));
    }

    public function create(?int $user_id = null)
    {
        $recipient = null;
        if ($user_id) {
            $recipient = User::find($user_id);
        }
        return view('messages.create', compact('recipient'));
    }
}
