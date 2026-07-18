@props(['type' => 'card', 'count' => 1])

@for($i = 0; $i < $count; $i++)
    @switch($type)
        @case('card')
            <div class="bg-white rounded shadow-md overflow-hidden border border border-secondary animate-pulse">
                <div style="height: 192px;" class="bg-light"></div>
                <div class="p-4 d-flex flex-column gap-2">
                    <div class="h-5 bg-light rounded w-3/4"></div>
                    <div class="h-4 bg-light rounded w-100"></div>
                    <div class="h-4 bg-light rounded w-1/2"></div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-t border border-secondary">
                        <div class="h-6 bg-light rounded w-24"></div>
                        <div style="height: 40px;" class="w-10 bg-light rounded"></div>
                    </div>
                </div>
            </div>
            @break

        @case('text')
            <div class="animate-pulse d-flex flex-column gap-2">
                <div class="h-4 bg-light rounded w-100"></div>
                <div class="h-4 bg-light rounded w-5/6"></div>
                <div class="h-4 bg-light rounded w-4/6"></div>
            </div>
            @break

        @case('avatar')
            <div class="animate-pulse d-flex align-items-center space-x-3">
                <div style="height: 40px;" class="w-10 bg-light rounded-pill"></div>
                <div class="d-flex flex-column gap-2 d-flex-1">
                    <div class="h-4 bg-light rounded w-1/3"></div>
                    <div class="h-3 bg-light rounded w-1/4"></div>
                </div>
            </div>
            @break

        @case('table')
            <div class="animate-pulse">
                <div style="height: 40px;" class="bg-light rounded-t-lg mb-1"></div>
                @for($j = 0; $j < 5; $j++)
                    <div style="height: 48px;" class="bg-light mb-0.5 d-flex align-items-center px-4 space-x-4">
                        <div class="h-4 bg-light rounded w-1/6"></div>
                        <div class="h-4 bg-light rounded w-1/4"></div>
                        <div class="h-4 bg-light rounded w-1/5"></div>
                        <div class="h-4 bg-light rounded w-1/6"></div>
                    </div>
                @endfor
            </div>
            @break

        @case('stat')
            <div class="bg-white rounded shadow-md p-6 animate-pulse">
                <div class="h-4 bg-light rounded w-1/2 mb-3"></div>
                <div style="height: 32px;" class="bg-light rounded w-2/3"></div>
            </div>
            @break
    @endswitch
@endfor
