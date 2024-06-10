<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-400 leading-tight">
            {{ __('Nuestros caballos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 px-4 pt-4 pb-4">
                    @foreach ($caballos as $caballo)
                        <div class="bg-white dark:bg-gray-700 p-5 rounded-lg shadow-lg pt-4 pb-4 px-4">
                            <p class="font-bold text-lg text-gray-600 dark:text-gray-400 text-center pb-4">{{ $caballo->nombre }}</p>
                            <img src="{{ asset('images/' . strtolower(str_replace(' ', '-', $caballo->nombre)) . '.jpg') }}" alt="{{ $caballo->nombre }}" class="object-cover w-60 h-60 mx-auto">
                            <p class="text-lg text-gray-600 dark:text-gray-400 pb-4 pt-4 text-center">{{ $caballo->observaciones }}</p>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
