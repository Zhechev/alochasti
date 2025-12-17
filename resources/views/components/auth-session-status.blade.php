@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert alert-info py-2 mb-3']) }} role="alert">
        {{ $status }}
    </div>
@endif
