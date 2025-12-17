@php
    /** @var \App\Models\Item|null $item */
    /** @var \Illuminate\Support\Collection<int, \App\Models\City>|array<int, \App\Models\City>|null $cities */
    /** @var \Illuminate\Support\Collection<int, \App\Models\Tag>|array<int, \App\Models\Tag>|null $tags */
@endphp

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<div class="row g-3">
    <div class="col-12">
        <x-input-label for="title" :value="__('Title')" />
        <x-text-input id="title" name="title" type="text" :value="old('title', $item?->title)" required />
        <x-input-error :messages="$errors->get('title')" />
    </div>

    <div class="col-12">
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" class="form-control" rows="5" required>{{ old('description', $item?->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" />
    </div>

    <div class="col-12 col-md-6">
        <x-input-label for="category_id" :value="__('Category')" />
        <select id="category_id" name="category_id" class="form-select" required>
            <option value="">{{ __('Select a category') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $item?->category_id) == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" />
    </div>

    <div class="col-12 col-md-6">
        <x-input-label for="city_id" :value="__('City')" />
        <select id="city_id" name="city_id" class="form-select" required>
            <option value="">{{ __('Select a city') }}</option>
            @foreach (($cities ?? []) as $cityOption)
                <option value="{{ $cityOption->id }}" @selected(old('city_id', $item?->city_id) == $cityOption->id)>
                    {{ $cityOption->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('city_id')" />
    </div>

    <div class="col-12 col-md-6">
        <x-input-label for="tag_ids" :value="__('Tags (optional)')" />
        <select id="tag_ids" name="tag_ids[]" class="form-select" multiple size="6">
            @php
                $selectedTagIds = collect(old('tag_ids', $item?->tags?->pluck('id')->all() ?? []))
                    ->map(fn ($v) => (int) $v)
                    ->all();
            @endphp

            @foreach (($tags ?? []) as $tagOption)
                <option value="{{ $tagOption->id }}" @selected(in_array($tagOption->id, $selectedTagIds, true))>
                    {{ $tagOption->name }}
                </option>
            @endforeach
        </select>
        <div class="form-text">{{ __('Hold Ctrl (Windows/Linux) or Cmd (Mac) to select multiple.') }}</div>
        <x-input-error :messages="$errors->get('tag_ids')" />
        <x-input-error :messages="$errors->get('tag_ids.*')" />
    </div>

    <div class="col-12 col-md-6">
        <x-input-label for="weight" :value="__('Weight (optional)')" />
        <x-text-input id="weight" name="weight" type="number" step="0.01" min="0" :value="old('weight', $item?->weight)" />
        <x-input-error :messages="$errors->get('weight')" />
    </div>

    <div class="col-12 col-md-6">
        <x-input-label for="dimensions" :value="__('Dimensions (optional)')" />
        <x-text-input id="dimensions" name="dimensions" type="text" :value="old('dimensions', $item?->dimensions)" />
        <x-input-error :messages="$errors->get('dimensions')" />
        <div class="form-text">{{ __('Example: 30x20x10 cm') }}</div>
    </div>

    @if ($item)
        <div class="col-12 col-md-6">
            <x-input-label for="status" :value="__('Status')" />
            <select id="status" name="status" class="form-select" required>
                @foreach (\App\Enums\ItemStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected(old('status', $item->status->value) === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('status')" />
        </div>
    @endif

    <div class="col-12">
        <x-input-label for="photos" :value="__('Photos (optional)')" />
        <input id="photos" name="photos[]" type="file" class="form-control" multiple accept="image/*" />
        <x-input-error :messages="$errors->get('photos')" />
        <x-input-error :messages="$errors->get('photos.*')" />
        <div class="form-text">{{ __('You can upload multiple photos. Max 4MB each.') }}</div>
    </div>

    @if ($item && $item->relationLoaded('photos') && $item->photos->isNotEmpty())
        <div class="col-12">
            <div class="d-flex flex-wrap gap-3">
                @foreach ($item->photos as $photo)
                    <label class="d-flex flex-column align-items-start gap-1">
                        <img src="{{ $photo->url() }}" alt="" class="rounded border" style="width: 140px; height: 100px; object-fit: cover;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="delete_photo_ids[]" value="{{ $photo->id }}" id="delete_photo_{{ $photo->id }}">
                            <span class="form-check-label small" for="delete_photo_{{ $photo->id }}">
                                {{ __('Delete') }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
            <x-input-error :messages="$errors->get('delete_photo_ids')" />
            <x-input-error :messages="$errors->get('delete_photo_ids.*')" />
        </div>
    @endif
</div>


