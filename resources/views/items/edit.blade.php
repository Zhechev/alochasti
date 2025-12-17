<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ __('Edit Item') }}</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('items.show', $item) }}" class="btn btn-outline-secondary">{{ __('View') }}</a>
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">{{ __('Back to feed') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('items._form', ['item' => $item, 'cities' => $cities, 'tags' => $tags])

                <div class="mt-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex gap-2">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                        <a href="{{ route('items.show', $item) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                    </div>
                </div>
            </form>

            <hr class="my-4">

            <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">{{ __('Delete item') }}</button>
            </form>
        </div>
    </div>
</x-app-layout>


