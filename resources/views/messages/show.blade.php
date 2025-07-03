@extends('layouts.app')

@section('header')
Conversación con {{ $partner->name ?? 'Usuario' }}
@endsection

@section('content')
<div class="container mx-auto py-6 max-w-2xl" x-data="{
    // Función para desplazarse al final de los mensajes
    scrollToBottom() {
        const messageContainer = this.$refs.messageContainer;
        if (messageContainer) {
            // Usar $nextTick para asegurar que el DOM se ha actualizado
            this.$nextTick(() => {
                messageContainer.scrollTop = messageContainer.scrollHeight;
            });
        }
    },
    // Función para recargar los mensajes
    refreshMessages() {
        // Usamos fetch para obtener solo el contenido de la lista de mensajes
        // Sin recargar toda la página, lo que es más eficiente para un chat.
        fetch(window.location.href) 
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                // Seleccionamos solo el contenido del contenedor de mensajes
                const newMessagesHtml = doc.querySelector('#message-list').innerHTML;
                this.$refs.messageContainer.innerHTML = newMessagesHtml;
                this.scrollToBottom(); // Desplazarse al final después de cargar los nuevos mensajes
            })
            .catch(error => console.error('Error al recargar mensajes:', error));
    },
    init() {
        this.scrollToBottom(); // Desplazarse al final al cargar la página inicialmente
        // Configurar un intervalo para recargar los mensajes cada 5 segundos (ejemplo)
        // En un entorno de producción real, usarías WebSockets para una actualización instantánea.
        setInterval(() => {
            this.refreshMessages();
        }, 5000); // Refresca cada 5 segundos
    }
}">
    <h1 class="text-2xl font-bold mb-4">Conversación con {{ $partner->name ?? 'Usuario Eliminado' }}</h1>

    @if(session('success'))
    {{-- Mensaje de éxito con Alpine.js para ocultar después de 3 segundos --}}
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition.opacity.duration.500ms class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="space-y-4 h-96 overflow-y-auto p-2 border border-gray-200 rounded-md mb-4" x-ref="messageContainer" id="message-list">
            @forelse($messages as $message)
                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs sm:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id === Auth::id() ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                        <p class="text-xs font-semibold mb-1">
                            {{ $message->sender_id === Auth::id() ? 'Tú' : ($message->sender->name ?? 'Usuario Eliminado') }}
                        </p>
                        <p>{{ $message->message_content }}</p>
                        <p class="text-right text-xs mt-1 {{ $message->sender_id === Auth::id() ? 'text-indigo-200' : 'text-gray-500' }}">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->sender_id === Auth::id())
                                @if($message->read_at)
                                    <i class="fas fa-check-double ml-1" title="Leído {{ $message->read_at->format('H:i') }}"></i>
                                @else
                                    <i class="fas fa-check ml-1" title="Enviado"></i>
                                @endif
                            @endif
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">No hay mensajes en esta conversación.</p>
            @endforelse
        </div>

        {{-- Formulario para enviar un nuevo mensaje --}}
        <form action="{{ route('messages.store') }}" method="POST" class="flex items-center gap-2" @submit.prevent="
            const form = $event.target;
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                }
            })
            .then(response => {
                // Verificar si la respuesta es JSON antes de intentar parsearla
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.indexOf('application/json') !== -1) {
                    return response.json();
                } else {
                    // Si no es JSON, asumir que es un error del servidor o una redirección
                    console.error('Respuesta no JSON:', response);
                    // Aquí podrías mostrar un mensaje de error más amigable en la UI
                    // o simplemente dejar que el refreshMessages maneje la falta de actualización.
                    return Promise.reject('Respuesta no JSON');
                }
            })
            .then(data => {
                if (data.success) {
                    form.reset(); // Limpiar el formulario
                    this.refreshMessages(); // Recargar los mensajes para mostrar el nuevo
                } else if (data.error) {
                    // Mostrar error en la consola o en un elemento de la UI, no con alert()
                    console.error('Error del servidor:', data.error); 
                } else if (data.errors) {
                    // Mostrar errores de validación en la consola o en un elemento de la UI
                    let errorMessages = Object.values(data.errors).flat().join('\\n');
                    console.error('Errores de validación:\\n', errorMessages);
                }
            })
            .catch(error => {
                console.error('Error al enviar mensaje:', error);
                // Mostrar un mensaje genérico de error en la consola o en un elemento de la UI
            });
        ">
            @csrf
            <input type="hidden" name="recipient_id" value="{{ $partner->id }}">
            <textarea name="message_content" rows="1" class="flex-1 border rounded-md px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500 resize-none" placeholder="Escribe tu mensaje..." required></textarea>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Enviar
            </button>
        </form>
        @error('message_content')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="flex justify-start mt-4">
        <a href="{{ route('messages.index') }}" class="px-4 py-2 bg-gray-300 rounded-md hover:bg-gray-400 text-gray-800">Volver a Conversaciones</a>
    </div>
</div>
@endsection
