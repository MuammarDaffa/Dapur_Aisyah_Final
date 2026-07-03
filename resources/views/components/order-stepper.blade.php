@props(['currentStatus'])

@php
    $steps = [
        'pending_payment' => ['label' => 'Menunggu Pembayaran', 'icon' => '1'],
        'processing'      => ['label' => 'Diproses',           'icon' => '2'],
        'on_delivery'     => ['label' => 'Sedang Dikirim',     'icon' => '3'],
        'completed'       => ['label' => 'Selesai',            'icon' => '4'],
    ];

    $isCancelled = $currentStatus === 'cancelled';
    $statusKeys = array_keys($steps);
    $currentIndex = array_search($currentStatus, $statusKeys);
    if ($currentIndex === false) $currentIndex = -1;
@endphp

<div class="w-full">
    @if($isCancelled)
        {{-- Cancelled State --}}
        <div class="flex items-center justify-center py-6">
            <div class="bg-red-50 border border-red-200 rounded-xl px-6 py-4 flex items-center gap-3">
                <span class="text-3xl">❌</span>
                <div>
                    <p class="font-semibold text-red-700">Pesanan Dibatalkan</p>
                    <p class="text-sm text-red-500">Pesanan ini telah dibatalkan</p>
                </div>
            </div>
        </div>
    @else
        {{-- Stepper --}}
        <div class="flex items-center justify-between w-full">
            @foreach($steps as $key => $step)
                @php
                    $stepIndex = array_search($key, $statusKeys);
                    $isCompleted = $stepIndex <= $currentIndex;
                    $isActive = $stepIndex === $currentIndex;
                    $isLast = $stepIndex === count($steps) - 1;
                @endphp

                {{-- Step Circle + Label --}}
                <div class="flex flex-col items-center relative z-10 {{ $isLast ? '' : 'flex-1' }}">
                    <div class="flex items-center w-full">
                        {{-- Circle --}}
                        <div class="flex flex-col items-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg shadow-md transition-all duration-300
                                {{ $isActive ? 'bg-gradient-to-br from-orange-400 to-amber-500 text-white ring-4 ring-orange-200 scale-110' :
                                   ($isCompleted ? 'bg-gradient-to-br from-green-400 to-emerald-500 text-white' : 'bg-gray-200 text-gray-400') }}">
                                {{ $step['icon'] }}
                            </div>
                            <span class="mt-2 text-xs font-medium text-center max-w-[80px] leading-tight
                                {{ $isActive ? 'text-orange-600 font-semibold' : ($isCompleted ? 'text-green-600' : 'text-gray-400') }}">
                                {{ $step['label'] }}
                            </span>
                        </div>

                        {{-- Connecting Line --}}
                        @if(!$isLast)
                            <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-500
                                {{ $isCompleted && $stepIndex < $currentIndex ? 'bg-gradient-to-r from-green-400 to-green-500' : 'bg-gray-200' }}">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
