<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ __('My Items') }}</h2>
            <a href="{{ route('items.create') }}" class="btn btn-primary">{{ __('Post an item') }}</a>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @livewire('items.feed', [
        'categories' => $categories,
        'cities' => $cities,
        'tags' => $tags,
        'onlyMine' => true,
    ])
</x-app-layout>


