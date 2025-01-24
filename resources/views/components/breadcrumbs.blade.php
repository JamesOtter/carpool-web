<nav class="text-sm font-medium text-gray-500" aria-label="breadcrumb">
    <ol class="flex space-x-1 pb-2">
        @foreach ($breadcrumbs as $breadcrumb)
            @if (!$loop->last)
                <li class="flex items-center">
                    <a
                        href="{{ $breadcrumb['link'] }}"
                        class="text-gray-500 hover:text-gray-700 transition"
                    >
                        {{ $breadcrumb['name'] }}
                    </a>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </li>
            @else
                <li class="text-gray-700">{{ $breadcrumb['name'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>
