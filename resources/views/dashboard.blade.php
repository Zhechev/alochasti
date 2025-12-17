<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="mb-0">{{ __("You're logged in!") }}</p>
        </div>
    </div>
</x-app-layout>
