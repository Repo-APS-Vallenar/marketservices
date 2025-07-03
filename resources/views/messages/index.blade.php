@extends('layouts.app')

@section('header')
Mis Conversaciones
@endsection

@section('content')
<div class="container mx-auto py-6 max-w-4xl">
    <h1 class="text-2xl font-bold mb-4">Mis Conversaciones</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow rounded overflow-hidden">
        <x-responsive-table>
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50 rounded-tl-md">Conversación con</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Último Mensaje</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-50">Fecha del Último Mensaje</th>
                        <th class="px-6 py-3 bg-gray-50 rounded-tr-md">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($conversations as $partnerId => $messagesInConversation)
                        @php
                            $partner = $conversationPartners->get($partnerId);
                            $latestMessage = $messagesInConversation->sortByDesc('created_at')->first();
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $partner->name ?? 'Usuario Eliminado' }}
                            </td>
                            <td class="px-6 py-4">
                                {{ Str::limit($latestMessage->message_content, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $latestMessage->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                {{-- CAMBIO CLAVE AQUÍ: Usar la nueva ruta y pasar el ID del partner --}}
                                <a href="{{ route('messages.show_conversation', $partner->id) }}" class="text-indigo-600 hover:text-indigo-900">Ver Conversación</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No tienes conversaciones.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </x-responsive-table>
    </div>
</div>
@endsection
