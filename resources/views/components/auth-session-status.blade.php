@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-700 bg-green-50 p-3 rounded-lg border border-green-200 text-center']) }}>
        {{ $status }}
    </div>
@endif
