<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                Portal Siswa
            </span>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">
                Absensi Harian
            </h1>
        </div>
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium">
            <x-atoms.icon variant="attendance" size="sm" color="#086375" />
            <span>{{ $siswa->kelas_label ?? '-' }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[320px_minmax(0,1fr)] gap-8">
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm h-fit">
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-full bg-[#086375] text-white flex items-center justify-center text-2xl font-bold shadow-sm">
                    {{ $siswa->initials }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $siswa->nama }}</h2>
                <p class="text-sm text-gray-500">NIS: {{ $siswa->nis ?? '-' }}</p>
            </div>
        </div>

        <div class="space-y-8">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                            <x-atoms.icon variant="attendance" size="md" color="#059669" />
                        </div>
                        <h3 class="font-bold text-gray-900 text-[15px]">Rekap Kehadiran</h3>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <input type="month" wire:model.live="bulan" class="w-full sm:w-auto border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-brand-dark focus:border-brand-dark">
                        <x-atoms.button wire:click="exportExcel" variant="primary" class="!py-1.5 !px-3 whitespace-nowrap">
                            Unduh
                        </x-atoms.button>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 flex flex-col items-center justify-center text-center">
                        <span class="text-3xl font-bold text-emerald-700">{{ $rekap['Hadir'] }}</span>
                        <span class="text-xs font-semibold text-emerald-600 mt-1 uppercase tracking-wide">Hadir</span>
                    </div>
                    <div class="bg-sky-50 border border-sky-100 rounded-xl p-4 flex flex-col items-center justify-center text-center">
                        <span class="text-3xl font-bold text-sky-700">{{ $rekap['Sakit'] }}</span>
                        <span class="text-xs font-semibold text-sky-600 mt-1 uppercase tracking-wide">Sakit</span>
                    </div>
                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 flex flex-col items-center justify-center text-center">
                        <span class="text-3xl font-bold text-amber-700">{{ $rekap['Izin'] }}</span>
                        <span class="text-xs font-semibold text-amber-600 mt-1 uppercase tracking-wide">Izin</span>
                    </div>
                    <div class="bg-red-50 border border-red-100 rounded-xl p-4 flex flex-col items-center justify-center text-center">
                        <span class="text-3xl font-bold text-red-700">{{ $rekap['Alpha'] }}</span>
                        <span class="text-xs font-semibold text-red-600 mt-1 uppercase tracking-wide">Alpha</span>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-5">
                    <div class="p-2 rounded-xl bg-slate-100 text-slate-700">
                        <x-atoms.icon variant="calendar" size="md" color="#334155" />
                    </div>
                    <h3 class="font-bold text-gray-900 text-[15px]">Histori Absensi</h3>
                </div>

                @if($history->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 text-sm text-slate-500 px-4 py-6 text-center">
                        Belum ada riwayat absensi.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($history as $entry)
                            <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-100 bg-slate-50 px-4 py-3">
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $entry->hari }}</div>
                                    <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($entry->tanggal_kehadiran)->format('d M Y') }}</div>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if($entry->status === 'Hadir') bg-emerald-100 text-emerald-700
                                    @elseif($entry->status === 'Izin') bg-amber-100 text-amber-700
                                    @elseif($entry->status === 'Sakit') bg-sky-100 text-sky-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ $entry->status }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-5">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
