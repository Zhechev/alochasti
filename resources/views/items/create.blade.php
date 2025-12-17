<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ __('Create Item') }}</h2>
            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">{{ __('Back to feed') }}</a>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
                @csrf

                @include('items._form', ['item' => null, 'cities' => $cities, 'tags' => $tags])

                <div class="mt-4 d-flex gap-2">
                    <x-primary-button>{{ __('Create') }}</x-primary-button>
                    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>


