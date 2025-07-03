@extends('layouts.app')

@section('header')
Mi Perfil de Proveedor
@endsection

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
    {{-- ELIMINADO: overflow-hidden de este div para permitir que el dropdown se extienda --}}
    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-900">Editar Perfil de Proveedor</h2>

        {{-- Los mensajes de éxito/error/validación son manejados globalmente por layouts/app.blade.php --}}

        <form method="POST" action="{{ route('provider.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Foto de Perfil -->
            <div>
                <label for="profile_picture" class="block text-sm font-medium text-gray-700">Foto de Perfil</label>
                <div class="mt-1 flex items-center">
                    @if($serviceProvider->profile_picture)
                    <img src="{{ Storage::url($serviceProvider->profile_picture) }}" alt="Foto de Perfil" class="h-20 w-20 rounded-full object-cover mr-4">
                    @else
                    <img src="https://placehold.co/80x80/cccccc/333333?text=Sin+Foto" alt="Sin Foto" class="h-20 w-20 rounded-full object-cover mr-4">
                    @endif
                    <input type="file" name="profile_picture" id="profile_picture" class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-indigo-50 file:text-indigo-700
                        hover:file:bg-indigo-100">
                </div>
                @error('profile_picture')<div class="mt-2 text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <!-- Número de Teléfono -->
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Número de Teléfono</label>
                <input type="text" name="phone_number" id="phone_number"
                    value="{{ old('phone_number', $serviceProvider->phone_number) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                @error('phone_number')<div class="mt-2 text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <!-- Biografía / Descripción -->
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700">Biografía / Descripción</label>
                <textarea name="bio" id="bio" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Describe tu experiencia, habilidades y lo que te hace especial.">{{ old('bio', $serviceProvider->bio) }}</textarea>
                @error('bio')<div class="mt-2 text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <!-- Selección de categorías que ofrece el proveedor (MEJORADO CON CHECKBOXES) -->
            <div x-data="{
                open: false,
                allCategories: {{ json_encode($allCategories->mapWithKeys(fn($cat) => [$cat->id => $cat->name])) }},
                selectedCategoryIds: {{ json_encode(old('categories', $providerCategoriesIds)) }},
                get selectedCategoryNames() {
                    return this.selectedCategoryIds.map(id => this.allCategories[id]);
                },
                get displayCategoriesText() {
                    if (this.selectedCategoryIds.length === 0) {
                        return 'Selecciona categorías...';
                    } else if (this.selectedCategoryIds.length <= 3) {
                        // Unir los nombres y truncar si es demasiado largo para el botón
                        let names = this.selectedCategoryNames.join(', ');
                        return names.length > 30 ? names.substring(0, 27) + '...' : names; // Ajusta el límite si es necesario
                    } else {
                        return 'Categorías seleccionadas: ' + this.selectedCategoryIds.length;
                    }
                }
            }" @click.away="open = false">
                <label for="categories-dropdown" class="block text-sm font-medium text-gray-700 mb-1">Categorías que ofreces</label>
                <div class="relative">
                    <button type="button" @click="open = !open" id="categories-dropdown"
                        class="relative w-full cursor-default rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm">
                        <span class="block" x-text="displayCategoriesText"></span>
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.25 8.75a.75.75 0 011.1 1.02L10 15.148l2.7-2.908a.75.75 0 011.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.55-.24z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </button>

                    <ul x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute z-10 mt-1 max-h-[300px] w-full overflow-y-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm" {{-- Ajustado max-h a un valor fijo para mejor control --}}
                        role="listbox" aria-labelledby="categories-dropdown">
                        @foreach($allCategories as $category)
                        <li class="text-gray-900 relative cursor-default select-none py-2 pl-3 pr-9 hover:bg-indigo-50 hover:text-indigo-900"
                            id="category-{{ $category->id }}" role="option"
                            @click="
                                    if (selectedCategoryIds.includes({{ $category->id }})) {
                                        selectedCategoryIds = selectedCategoryIds.filter(id => id !== {{ $category->id }});
                                    } else {
                                        selectedCategoryIds.push({{ $category->id }});
                                    }
                                ">
                            <div class="flex items-center">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    :checked="selectedCategoryIds.includes({{ $category->id }})"
                                    @click.stop="" {{-- Evita que el clic en el checkbox cierre el dropdown --}}>
                                <span class="ml-3 block" x-text="'{{ $category->name }}'"></span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <p class="mt-2 text-sm text-gray-500">Selecciona una o más categorías relacionadas con tus servicios.</p>
                @error('categories')<div class="mt-2 text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <!-- Áreas de Servicio -->
            <div>
                <label for="service_areas" class="block text-sm font-medium text-gray-700">Áreas de Servicio</label>
                <input type="text" name="service_areas" id="service_areas"
                    value="{{ old('service_areas', $serviceProvider->service_areas) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Ej: Santiago Centro, Providencia, Las Condes">
                @error('service_areas')<div class="mt-2 text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <!-- Certificación (Opcional) -->
            <div>
                <label for="certification" class="block text-sm font-medium text-gray-700">Certificación (Opcional)</label>
                <input type="text" name="certification" id="certification"
                    value="{{ old('certification', $serviceProvider->certification) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    placeholder="Ej: Licencia Clase B, Certificado SEC">
                @error('certification')<div class="mt-2 text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>


            <!-- Botón de Guardar -->
            <div class="flex items-center justify-end mt-6">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection