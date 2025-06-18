<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message_text' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message_text' => $request->message_text,
        ]);

        return response()->json(['message' => 'Mensaje enviado', 'data' => $message], 201);
    }

    public function getConversations()
    {
        $user = Auth::user();

        $conversationPartners = Message::where('sender_id', $user->id)
                                    ->orWhere('receiver_id', $user->id)
                                    ->pluck('sender_id')
                                    ->merge(Message::where('sender_id', $user->id)
                                                ->orWhere('receiver_id', $user->id)
                                                ->pluck('receiver_id'))
                                    ->unique()
                                    ->filter(function ($id) use ($user) {
                                        return $id !== $user->id;
                                    });

        $conversations = [];
        foreach ($conversationPartners as $partnerId) {
            $partner = User::find($partnerId);
            if ($partner) {
                $lastMessage = Message::where(function ($query) use ($user, $partnerId) {
                                    $query->where('sender_id', $user->id)
                                          ->where('receiver_id', $partnerId);
                                })
                                ->orWhere(function ($query) use ($user, $partnerId) {
                                    $query->where('sender_id', $partnerId)
                                          ->where('receiver_id', $user->id);
                                })
                                ->latest()
                                ->first();

                $conversations[] = [
                    'partner' => $partner,
                    'last_message' => $lastMessage,
                ];
            }
        }

        return response()->json(['conversations' => $conversations]);
    }

    public function getMessagesWithUser(User $otherUser)
    {
        $user = Auth::user();

        $messages = Message::where(function ($query) use ($user, $otherUser) {
                                    $query->where('sender_id', $user->id)
                                          ->where('receiver_id', $otherUser->id);
                                })
                                ->orWhere(function ($query) use ($user, $otherUser) {
                                    $query->where('sender_id', $otherUser->id)
                                          ->where('receiver_id', $user->id);
                                })
                                ->orderBy('created_at', 'asc')
                                ->get();

        return response()->json(['messages' => $messages]);
    }
} 