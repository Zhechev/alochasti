<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ $item->title }}</h2>
            <div class="d-flex gap-2">
                @can('update', $item)
                    <a href="{{ route('items.edit', $item) }}" class="btn btn-outline-secondary">{{ __('Edit') }}</a>
                @endcan
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">{{ __('Back to feed') }}</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge text-bg-secondary">{{ $item->category->name }}</span>
                        <span class="badge text-bg-light border">{{ $item->city->name }}</span>
                        @if ($item->status === \App\Enums\ItemStatus::Gifted)
                            <span class="badge text-bg-success">{{ __('Gifted') }}</span>
                        @else
                            <span class="badge text-bg-primary">{{ __('Available') }}</span>
                        @endif
                        @foreach ($item->tags as $tag)
                            <span class="badge text-bg-dark">{{ $tag->name }}</span>
                        @endforeach
                    </div>

                    <p class="mb-0" style="white-space: pre-wrap;">{{ $item->description }}</p>
                </div>
            </div>

            @if ($item->photos->isNotEmpty())
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach ($item->photos as $photo)
                                <div class="col-6 col-md-4">
                                    <img src="{{ $photo->url() }}" alt="" class="img-fluid rounded">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-12 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="mb-2">
                        <div class="text-secondary small">{{ __('Posted by') }}</div>
                        <div class="fw-semibold">{{ $item->user->name }}</div>
                    </div>

                    <div class="row g-2">
                        @if ($item->weight)
                            <div class="col-12">
                                <div class="text-secondary small">{{ __('Weight') }}</div>
                                <div>{{ $item->weight }}</div>
                            </div>
                        @endif

                        @if ($item->dimensions)
                            <div class="col-12">
                                <div class="text-secondary small">{{ __('Dimensions') }}</div>
                                <div>{{ $item->dimensions }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    @livewire('items.interactions', [
                        'itemId' => $item->id,
                    ])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


