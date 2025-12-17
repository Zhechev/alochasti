<div class="card shadow-sm position-relative">
    <div wire:loading.flex class="position-absolute top-0 start-0 w-100 h-100 align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 10;">
        <div class="spinner-border text-primary" role="status" aria-label="{{ __('Loading') }}"></div>
    </div>

    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label">{{ __('Search') }}</label>
                <input type="search" class="form-control" placeholder="{{ __('Title or tag...') }}" wire:model.live.debounce.400ms="search">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">{{ __('Category') }}</label>
                <select class="form-select" wire:model.live="categoryId">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">{{ __('City') }}</label>
                <select class="form-select" wire:model.live="cityId">
                    <option value="">{{ __('All') }}</option>
                    @foreach ($cities as $cityOption)
                        <option value="{{ $cityOption['id'] }}">{{ $cityOption['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label">{{ __('Tags') }}</label>
                <select class="form-select" wire:model.live="tagIds" multiple size="6">
                    @foreach ($tags as $tagOption)
                        <option value="{{ $tagOption['id'] }}">{{ $tagOption['name'] }}</option>
                    @endforeach
                </select>
                <div class="form-text">{{ __('Select one or more tags to filter. (Hold Ctrl/Cmd for multiple)') }}</div>
            </div>

            <div class="col-6 col-md-1">
                <label class="form-label">{{ __('Status') }}</label>
                <select class="form-select" wire:model.live="status">
                    <option value="">{{ __('All') }}</option>
                    <option value="available">{{ __('Available') }}</option>
                    <option value="gifted">{{ __('Gifted') }}</option>
                </select>
            </div>

            <div class="col-6 col-md-1">
                <label class="form-label">{{ __('Sort') }}</label>
                <select class="form-select" wire:model.live="sort">
                    <option value="newest">{{ __('Newest') }}</option>
                    <option value="top">{{ __('Most upvoted') }}</option>
                </select>
            </div>

            <div class="col-12 d-flex justify-content-end">
                @php
                    $hasFilters = ($search !== '')
                        || !is_null($categoryId)
                        || !is_null($cityId)
                        || ($status !== '')
                        || !empty($tagIds)
                        || ($sort !== 'newest');
                @endphp

                @if ($hasFilters)
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearFilters" wire:loading.attr="disabled">
                        {{ __('Clear filters') }}
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="card-body border-top">
        @if ($items->count() === 0)
            <div class="text-secondary">{{ __('No items found.') }}</div>
        @else
            <div class="row g-3">
                @foreach ($items as $item)
                    @php
                        $score = (int) ($item->score ?? 0);
                        $myVote = $item->votes->first()?->value;
                        $thumb = $item->primaryPhoto?->url();
                    @endphp

                    <div class="col-12 col-md-6 col-lg-4" wire:key="item-{{ $item->id }}">
                        <div class="card h-100">
                            @if ($thumb)
                                <a href="{{ route('items.show', $item) }}">
                                    <img src="{{ $thumb }}" class="card-img-top gs-thumbnail" alt="">
                                </a>
                            @else
                                <a href="{{ route('items.show', $item) }}" class="text-decoration-none">
                                    <div class="ratio ratio-16x9 bg-light border-bottom d-flex align-items-center justify-content-center">
                                        <span class="text-secondary small">{{ __('No photo') }}</span>
                                    </div>
                                </a>
                            @endif

                            <div class="card-body">
                                <div class="d-flex justify-content-between gap-2">
                                    <h3 class="h6 mb-1">
                                        <a href="{{ route('items.show', $item) }}" class="text-decoration-none">
                                            {{ $item->title }}
                                        </a>
                                    </h3>

                                    @if ($item->status->value === 'gifted')
                                        <span class="badge text-bg-success align-self-start">{{ __('Gifted') }}</span>
                                    @else
                                        <span class="badge text-bg-primary align-self-start">{{ __('Available') }}</span>
                                    @endif
                                </div>

                                <div class="small text-secondary mb-2">
                                    {{ $item->category->name }} · {{ $item->city->name }} · {{ __('by') }} {{ $item->user->name }}
                                </div>

                                @if ($item->tags->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        @foreach ($item->tags as $tag)
                                            <button type="button"
                                                    class="badge text-bg-dark border-0"
                                                    wire:click="applyTag({{ $tag->id }})">
                                                {{ $tag->name }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="btn-group" role="group" aria-label="{{ __('Voting controls') }}">
                                        <button type="button"
                                                class="btn btn-sm {{ $myVote === 1 ? 'btn-success' : 'btn-outline-success' }}"
                                                wire:click="vote({{ $item->id }}, 1)"
                                                wire:loading.attr="disabled">
                                            +1
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm {{ $myVote === -1 ? 'btn-danger' : 'btn-outline-danger' }}"
                                                wire:click="vote({{ $item->id }}, -1)"
                                                wire:loading.attr="disabled">
                                            -1
                                        </button>
                                    </div>

                                    <div class="small">
                                        <span class="fw-semibold">{{ $score }}</span>
                                        <span class="text-secondary">{{ __('score') }}</span>
                                        <span class="text-secondary">·</span>
                                        <span class="text-secondary">{{ $item->comments_count }} {{ __('comments') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-white">
                                @if ($onlyMine)
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                            {{ __('View') }}
                                        </a>
                                        <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-secondary">
                                            {{ __('Edit') }}
                                        </a>
                                        @if ($item->status->value !== 'gifted')
                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    wire:click="markGifted({{ $item->id }})"
                                                    wire:loading.attr="disabled">
                                                {{ __('Mark gifted') }}
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <a href="{{ route('items.show', $item) }}" class="btn btn-sm btn-outline-primary w-100">
                                        {{ __('View details') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
