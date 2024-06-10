<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            {{ __('Mis reservas') }}
        </h2>
    </x-slot>
   
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 m-4">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">

                @if($reservas->isEmpty())
                    <p class="text-center text-gray-500">No hay reservas disponibles.</p>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 px-4 pt-4 pb-4">
                        @foreach ($reservas as $reserva)
                            <div class="bg-white dark:bg-gray-700 p-5 rounded-lg shadow-lg pt-4">
                                <div class="text-center">
                                    <p class="text-lg font-semibold dark:text-gray-400">{{ $reserva->fecha }}</p>
                                    <p class="text-lg font-semibold dark:text-gray-400">{{ \Carbon\Carbon::parse($reserva->hora)->format('H:i') }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 pb-4">Comentarios: 
                                        @if (is_null($reserva->comentarios))
                                            sin comentarios
                                        @else
                                            {{ Str::limit($reserva->comentarios, 20) }}
                                        
                                        @endif</p>

                                    <p class="text-lg text-gray-600 dark:text-gray-400 pb-4">{{ $reserva->getNombreCaballo() }}</p>
                                    <img src="{{ asset('images/' . strtolower(str_replace(' ', '-', $reserva->getNombreCaballo())) . '.jpg') }}" alt="{{ $reserva->getNombreCaballo() }}" class="object-cover w-60 h-60 mx-auto">

                                </div>
                                <div class="mt-4 flex justify-center pb-4">
                                    <form action="/editreserva/{{ $reserva->id }}" method="POST" onsubmit="return confirmarBorrado()">
                                        <a href="/editreserva/{{ $reserva->id }}" name="reservas.edit" class="text-sm bg-gray-500 hover:bg-gray-700 text-white py-1 px-4 rounded focus:outline-none focus:shadow-outline">Editar</a>
                                        <button type="submit" name="delete"  class="text-sm bg-red-500 hover:bg-red-700 text-white py-1 px-4 rounded focus:outline-none focus:shadow-outline">Borrar</button>
                                        {{ csrf_field() }}
                                    </form>
                                </div>
                            </div>
                        @endforeach

                    </div>
                    @endif
            </div>
        </div>
    </div>

    <script>
        function confirmarBorrado() {
            return confirm('¿Estás seguro de que quieres borrar esta reserva? Esta acción no se puede deshacer.');
        }
    </script>

</x-app-layout>