@props(['type' => 'card', 'count' => 1])

@for($i = 0; $i < $count; $i++)
    @switch($type)
        @case('card')
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 animate-pulse">
                <div class="h-48 bg-gray-200"></div>
                <div class="p-4 space-y-3">
                    <div class="h-5 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-full"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                        <div class="h-6 bg-gray-200 rounded w-24"></div>
                        <div class="w-10 h-10 bg-gray-200 rounded-xl"></div>
                    </div>
                </div>
            </div>
            @break

        @case('text')
            <div class="animate-pulse space-y-2">
                <div class="h-4 bg-gray-200 rounded w-full"></div>
                <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                <div class="h-4 bg-gray-200 rounded w-4/6"></div>
            </div>
            @break

        @case('avatar')
            <div class="animate-pulse flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                <div class="space-y-2 flex-1">
                    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                    <div class="h-3 bg-gray-200 rounded w-1/4"></div>
                </div>
            </div>
            @break

        @case('table')
            <div class="animate-pulse">
                <div class="h-10 bg-gray-200 rounded-t-lg mb-1"></div>
                @for($j = 0; $j < 5; $j++)
                    <div class="h-12 bg-gray-100 mb-0.5 flex items-center px-4 space-x-4">
                        <div class="h-4 bg-gray-200 rounded w-1/6"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/5"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/6"></div>
                    </div>
                @endfor
            </div>
            @break

        @case('stat')
            <div class="bg-white rounded-xl shadow-md p-6 animate-pulse">
                <div class="h-4 bg-gray-200 rounded w-1/2 mb-3"></div>
                <div class="h-8 bg-gray-200 rounded w-2/3"></div>
            </div>
            @break
    @endswitch
@endfor
