<div class="px-6 sm:px-8 py-6">
    <button
        type="button"
        wire:click="kembaliKeTingkat"
        class="inline-flex items-center text-xs text-gray-500 hover:text-brand-teal mb-5"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar Tingkat
    </button>

    <div class="mb-5">
        <h2 class="text-base font-semibold text-gray-800">Pilih Kelas</h2>
        <p class="text-sm text-gray-500 mt-1">Pilih kelas untuk melihat data {{ $assessmentName }} tingkat {{ $selectedTingkat }}.</p>
    </div>

    @if(count($tingkatKelasOptions) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($tingkatKelasOptions as $kelas)
                <button
                    type="button"
                    wire:key="{{ Str::slug($assessmentName) }}-kelas-{{ $kelas }}"
                    wire:click="pilihKelas(@js($kelas))"
                    class="group text-left bg-white border border-gray-200 rounded-xl p-6 shadow-sm transition-all duration-200 hover:border-brand-teal hover:shadow-md hover:-translate-y-0.5"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Kelas</p>
                            <h3 class="mt-2 text-lg font-semibold text-gray-800 group-hover:text-brand-teal">{{ $kelas }}</h3>
                        </div>
                        <div class="w-11 h-11 rounded-lg bg-teal-50 flex items-center justify-center text-brand-teal">
                            <x-atoms.icon variant="book" size="md" />
                        </div>
                    </div>
                    <div class="mt-5 flex items-center text-xs text-gray-400">
                        Lihat {{ $assessmentName }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </button>
            @endforeach
        </div>
    @else
        <div class="border border-dashed border-gray-300 rounded-xl py-12 text-center">
            <p class="text-sm text-gray-500">Belum ada data kelas untuk tingkat {{ $selectedTingkat }}.</p>
        </div>
    @endif
</div>
