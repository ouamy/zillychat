<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="h-screen w-screen p-0 m-0">
                <x-welcome :messages="$messages" />
            </div>
        </div>
    </div>
</x-app-layout>
