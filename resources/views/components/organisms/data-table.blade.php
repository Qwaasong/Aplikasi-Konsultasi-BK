@props([
    'headers' => [],
    'empty'   => 'Belum ada data.',
])

<div {{ $attributes->merge(['class' => 'flex-1 overflow-auto bg-white']) }}>
    <table class="w-full text-left border-collapse">
        @if(count($headers) > 0)
        <thead class="bg-gray-50">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        @endif

        <tbody class="text-sm">
            @if($slot->isNotEmpty())
                {{ $slot }}
            @else
                <tr>
                    <td colspan="{{ max(1, count($headers)) }}" class="px-6 py-8 text-center text-gray-400">
                        <div class="text-center py-8">
                            <p class="mt-2 text-sm text-gray-500">{{ $empty }}</p>
                        </div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    @if(isset($pagination))
        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            {{ $pagination }}
        </div>
    @endif
</div>
