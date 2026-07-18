@props(['currentStatus'])

@php
    $steps = [
        'processing'      => ['label' => 'Diproses',           'icon' => '1'],
        'on_delivery'     => ['label' => 'Sedang Dikirim',     'icon' => '2'],
        'completed'       => ['label' => 'Selesai',            'icon' => '3'],
    ];

    $isCancelled = $currentStatus === 'cancelled';
    $statusKeys = array_keys($steps);
    $currentIndex = array_search($currentStatus, $statusKeys);
    if ($currentIndex === false) $currentIndex = -1;
@endphp

<div class="w-100">
    @if($isCancelled)
        {{-- Cancelled State --}}
        <div class="d-flex align-items-center justify-content-center py-6">
            <div class="bg-danger text-white border border-red-200 rounded px-6 py-4 d-flex align-items-center g-3">
                <span class="fs-2">❌</span>
                <div>
                    <p class="fw-bold text-danger">Pesanan Dibatalkan</p>
                    <p class="fs-6 text-danger">Pesanan ini telah dibatalkan</p>
                </div>
            </div>
        </div>
    @else
        {{-- Stepper --}}
        <div class="d-flex align-items-center justify-content-between w-100">
            @foreach($steps as $key => $step)
                @php
                    $stepIndex = array_search($key, $statusKeys);
                    $isCompleted = $stepIndex <= $currentIndex;
                    $isActive = $stepIndex === $currentIndex;
                    $isLast = $stepIndex === count($steps) - 1;
                @endphp

                {{-- Step Circle + Label --}}
                <div class="d-flex d-flex-column align-items-center position-relative {{ $isLast ? '' : 'd-flex-1' }}">
                    <div class="d-flex align-items-center w-100">
                        {{-- Circle --}}
                        <div class="d-flex d-flex-column align-items-center">
                            <div style="width: 48px; height: 48px;" class="rounded-pill d-flex align-items-center justify-content-center fs-5 shadow-md {{ $isActive ? ' text-white ring-4 ring-orange-200 ' : ($isCompleted ? ' text-white' : 'bg-light text-secondary') }}">
                                {{ $step['icon'] }}
                            </div>
                            <span class="mt-2 small fw-medium text-center max-w-[80px] leading-tight {{ $isActive ? 'text-primary fw-bold' : ($isCompleted ? 'text-success' : 'text-secondary') }}">
                                {{ $step['label'] }}
                            </span>
                        </div>

                        {{-- Connecting Line --}}
                        @if(!$isLast)
                            <div class="d-flex-1 h-1 mx-2 rounded-pill {{ $isCompleted && $stepIndex < $currentIndex ? ' ' : 'bg-light' }}">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
