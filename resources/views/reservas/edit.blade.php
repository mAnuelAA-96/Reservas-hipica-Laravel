<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            {{ __('Editar reserva') }}
        </h2>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-5">
                <div class="flex-auto text-center text-2xl mt-4 mb-4 dark:text-white">Editar reserva</div>
            
                @if ($errors->has('mensaje'))
                    <div class="bg-red-500 text-white text-center p-4 mb-4 rounded">
                        {{ $errors->first('mensaje') }}
                    </div>
                @endif
            
                <form method="POST" action="/editreserva/{{ $reserva->id }}" class="w-1/2 mx-auto">
    
                    <div class="form-group mb-4">
                        <label for="fecha" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha</label>
                        <input type='date' name='fecha' id="fecha" value="{{ $reserva->fecha }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></input>
                        @if ($errors->has('fecha'))
                            <span class="text-danger dark:text-white dark:bg-gray-500">{{ $errors->first('fecha') }}</span>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label for="hora" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Hora</label>
                        <select name="hora" id="hora" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option value="10:00" {{ $reserva->hora == '10:00:00' ? 'selected' : '' }}>10:00</option>
                            <option value="11:00" {{ $reserva->hora == '11:00:00' ? 'selected' : '' }}>11:00</option>
                            <option value="12:00" {{ $reserva->hora == '12:00:00' ? 'selected' : '' }}>12:00</option>
                            <option value="13:00" {{ $reserva->hora == '13:00:00' ? 'selected' : '' }}>13:00</option>
                        </select>
                        @if ($errors->has('hora'))
                            <span class="text-danger dark:text-white dark:bg-gray-500">{{ $errors->first('hora') }}</span>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label for="caballo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Caballos</label>
                        <select name="caballo" id="caballo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            @foreach ($caballos as $caballo)
                                <option value="{{ $caballo->id }}"
                                    @if ($caballo->nombre == $reserva->getNombreCaballo())
                                        selected
                                    @endif>
                                    {{ $caballo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="comentarios" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Comentarios</label>
                        <textarea type='text' name='comentarios' id="comentarios" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">{{ $reserva->comentarios }}</textarea>
                        @if ($errors->has('comentarios'))
                            <span class="text-danger dark:text-white dark:bg-gray-500">{{ $errors->first('comentarios') }}</span>
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Confirmar reserva</button>
                        <a href="/misreservas" class="bg-blue-500 dark:bg-cyan-700 hover:bg-gray-700 text-white font-bold mr-8 py-2 px-4 rounded">Cancelar</a>
                    </div>
                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('fecha');
            dateInput.addEventListener('input', function() {
                const selectedDate = new Date(this.value);
                const day = selectedDate.getUTCDay();
                if (day !== 6 && day !== 0) {
                    alert('Solo se pueden seleccionar sábados y domingos.');
                    this.value = '';
                }
            });
        });
    </script>
    
</x-app-layout>