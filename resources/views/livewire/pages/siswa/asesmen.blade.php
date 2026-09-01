<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-violet-50 text-violet-700 border border-violet-100">
                Portal Siswa
            </span>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">
                Form Asesmen
            </h1>
        </div>
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium">
            <x-atoms.icon variant="assessment" size="sm" color="#086375" />
            <span>5 Form Tersedia</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($items as $item)
            @php
                $isExternalLink = str_starts_with($item['route'] ?? '', 'http');
            @endphp

            @if(empty($item['options']))
                <a
                    href="{{ $item['route'] }}"
                    @if($isExternalLink) target="_blank" rel="noopener noreferrer" @endif
                    class="group block h-full bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            @else
                <div class="group block h-full bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
            @endif
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 rounded-xl bg-[#086375]/10 text-[#086375]">
                        <x-atoms.icon variant="assessment" size="md" color="#086375" />
                    </div>
                    <span class="px-2 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                        {{ $item['badge'] }}
                    </span>
                </div>

                <h2 class="text-lg font-bold text-gray-900 mb-2">{{ $item['title'] }}</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    {{ $item['description'] }}
                </p>

                @if(!empty($item['options']))
                    <div class="mt-5 grid grid-cols-3 gap-2">
                        @foreach($item['options'] as $option)
                            <a href="{{ $option['route'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center px-2 py-2 rounded-lg bg-[#086375] text-white text-xs font-semibold hover:bg-[#064a5e] transition">
                                {{ $option['label'] }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-[#086375] group-hover:text-[#064a5e]">
                        <span>{{ $item['label'] }}</span>
                        <x-atoms.icon variant="arrow-right" size="sm" color="#086375" />
                    </div>
                @endif
            @if(empty($item['options']))
                </a>
            @else
                </div>
            @endif
        @endforeach
    </div>
</div>
