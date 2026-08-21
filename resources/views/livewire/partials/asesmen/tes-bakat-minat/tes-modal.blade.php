<div>
    <x-shared.modal name="form-peminatan" maxWidth="lg">

        <div class="flex flex-col h-full max-h-[80vh]">

            {{-- =====================================================
                HEADER
            ====================================================== --}}
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">

                <h2 class="text-base font-bold text-gray-900 leading-tight">
                    {{ $editingId ? 'Edit Tes Bakat Minat' : 'Tambah Tes Bakat Minat' }}
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $editingId
                        ? 'Perbarui hasil tes bakat minat siswa'
                        : 'Catat tes bakat minat siswa baru'
                    }}
                </p>

            </div>


            {{-- =====================================================
                BODY
            ====================================================== --}}
            <div
                class="px-6 py-4 overflow-y-auto modal-scroll grow"
                style="scrollbar-width: thin;"
            >

                {{-- =================================================
                    FORM BODY
                ================================================== --}}
                <div>


                    {{-- =================================================
                        SISWA
                    ================================================== --}}
                    <div class="mb-6">

                        <x-atoms.input-label for="siswa_id" size="sm">
                            Siswa
                        </x-atoms.input-label>

                        @php
                            $selectedStudent = collect($students)
                                ->firstWhere('id', $siswa_id);
                        @endphp

                        @if($selectedStudent)

                            <div class="bg-bg-light border border-teal-100/60 rounded-lg p-4 flex items-center justify-between">

                                <div class="flex items-center gap-3">

                                    <div class="w-[45px] h-[45px] bg-icon-bg text-primary rounded-full flex items-center justify-center font-bold text-[16px]">
                                        {{ $this->getInitials($selectedStudent->nama ?? '') }}
                                    </div>

                                    <div>

                                        <h3 class="text-[14px] font-bold text-gray-900">
                                            {{ $selectedStudent->nama ?? '-' }}
                                        </h3>

                                        <p class="text-[12px] text-gray-400 mt-0.5">
                                            Kelas {{ $selectedStudent->kelas_label ?? '-' }}
                                            {{ $selectedStudent->jurusan_label ?? '' }}
                                            - NIS {{ $selectedStudent->nis ?? '-' }}
                                        </p>

                                    </div>

                                </div>

                                <button
                                    type="button"
                                    wire:click="openStudentModal"
                                    class="text-[13px] font-bold text-gray-500 hover:text-gray-800 transition-colors"
                                >
                                    Ganti
                                </button>

                            </div>

                        @else

                            <div class="bg-bg-light border border-teal-100/60 rounded-lg p-5 flex flex-col items-center justify-center text-center">

                                <div class="w-[56px] h-[56px] bg-icon-bg rounded-full flex items-center justify-center mb-3 text-primary">

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        class="w-7 h-7"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                                        />
                                    </svg>

                                </div>

                                <h3 class="text-[15px] font-bold text-gray-700 mb-1">
                                    Tidak Ada Siswa Yang Dipilih
                                </h3>

                                <p class="text-[13px] text-gray-400 mb-4">
                                    Pilih Siswa Untuk Melanjutkan
                                </p>

                                <button
                                    type="button"
                                    wire:click="openStudentModal"
                                    class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md text-[13px] font-semibold transition-colors"
                                >
                                    Pilih Siswa
                                </button>

                            </div>

                        @endif

                        @error('siswa_id')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                        TANGGAL
                    ================================================== --}}
                    <div class="mb-6">

                        <x-atoms.input-label for="tanggal" size="sm">
                            Tanggal
                        </x-atoms.input-label>

                        <x-atoms.text-input
                            id="tanggal"
                            type="date"
                            wire:model="tanggal"
                            size="md"
                        />

                        @error('tanggal')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>




                    {{-- =================================================
                        DAFTAR PERNYATAAN BAKAT MINAT (8 KECERDASAN)
                    ================================================== --}}
                    <div class="mb-6">

                        <x-atoms.input-label size="sm">
                            Pernyataan Bakat Minat (8 Kecerdasan)
                        </x-atoms.input-label>

                        <p class="text-[11px] text-gray-400 mt-1.5 mb-3">
                            Centang pernyataan yang sesuai dengan siswa. Skor dan kecerdasan dominan dihitung otomatis.
                        </p>

                        <div
                            class="border border-gray-200 rounded-md p-4 space-y-6 max-h-[50vh] overflow-y-auto modal-scroll"
                            style="scrollbar-width: thin;"
                        >

                            @foreach(\App\Models\Peminatan::SECTIONS as $section)

                                <div>

                                    <p class="text-[13px] font-bold text-gray-800 mb-2">
                                        {{ $section }}
                                    </p>

                                    <div class="space-y-1.5">

                                        @foreach(\App\Models\Peminatan::QUESTION_GROUPS[$section] as $kode => $pertanyaan)

                                            <label class="flex items-start gap-2.5 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    value="{{ $kode }}"
                                                    wire:model.live="jawaban.{{ $section }}"
                                                    class="mt-0.5 w-4 h-4 rounded border-gray-300
                                                           text-primary focus:ring-primary
                                                           accent-primary cursor-pointer"
                                                >

                                                <span class="text-[13px] text-gray-700 leading-5">
                                                    {{ $kode }} - {{ $pertanyaan }}
                                                </span>

                                            </label>

                                        @endforeach

                                    </div>

                                </div>

                            @endforeach

                        </div>

                        @error('jawaban')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                        RINGKASAN SKOR KECERDASAN
                    ================================================== --}}
                    <div class="mb-6">

                        <div class="rounded-xl border border-teal-100 bg-teal-50/50 p-4">

                            <p class="text-[13px] font-bold text-gray-800 mb-3">
                                Ringkasan Skor Kecerdasan
                            </p>

                            <div class="space-y-1">

                                @foreach($this->skorKecerdasan as $item)

                                    <div class="flex justify-between items-center py-0.5">

                                        <span class="text-[13px] text-gray-600">
                                            {{ $item['section'] }}
                                        </span>

                                        <span class="text-[13px] font-bold text-primary">
                                            {{ $item['skor'] }}/{{ $item['total'] }}
                                        </span>

                                    </div>

                                @endforeach

                            </div>

                            <div class="mt-3 pt-3 border-t border-teal-100 text-center">

                                <p class="text-[11px] uppercase tracking-wider text-gray-500">
                                    Kecerdasan Dominan
                                </p>

                                <h3 class="mt-1 text-xl font-extrabold text-primary">
                                    {{ $this->dominantKecerdasan ?: '-' }}
                                </h3>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        HASIL
                    ================================================== --}}
                    <div class="mb-6">

                        <x-atoms.input-label for="hasil" size="sm">
                            Hasil Tes Bakat Minat (manual, opsional)
                        </x-atoms.input-label>

                        <textarea
                            wire:model="hasil"
                            rows="3"
                            class="w-full border border-gray-200 rounded-md p-4 text-[14px]
                            focus:border-primary focus:ring-1 focus:ring-primary resize-none">

                        </textarea>

                        @error('hasil')
                            <span class="text-red-500 text-[13px]">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                    <div class="mb-6">

                        <x-atoms.input-label for="catatan" size="sm">
                            Catatan Guru BK
                        </x-atoms.input-label>

                        <textarea
                            wire:model="catatan"
                            rows="5"
                            class="w-full border border-gray-200 rounded-md p-4 text-[14px]
                            focus:border-primary focus:ring-1 focus:ring-primary resize-none">

                        </textarea>

                    </div>

                </div>


            {{-- =====================================================
                FOOTER
            ====================================================== --}}
            <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl gap-3">

                <x-atoms.button
                    variant="secondary"
                    size="md"
                    x-on:click="show = false"
                >
                    Batal
                </x-atoms.button>

                <x-atoms.button wire:click="save">
                    {{ $editingId ? 'Perbarui Asesmen' : 'Simpan Asesmen' }}
                </x-atoms.button>

            </div>

            </div>

        </div>


        {{-- =========================================================
            MODAL PILIH SISWA
        ========================================================== --}}

        <div
            x-data="{ showStudentMenu: @entangle('showStudentModal') }"
            x-show="showStudentMenu"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-opacity"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;"
        >

            <div
                class="bg-white w-full max-w-[500px] rounded-xl shadow-2xl flex flex-col max-h-[80vh] overflow-hidden"
                @click.away="showStudentMenu = false"
            >

                {{-- HEADER --}}
                <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">

                    <h2 class="text-[20px] font-bold text-gray-900 leading-tight">
                        Pilih Siswa
                    </h2>

                    <p class="text-[13px] text-gray-500 mt-0.5">
                        Cari siswa berdasarkan nama atau NIS
                    </p>

                </div>


                {{-- BODY --}}
                <div
                    class="px-6 py-5 overflow-y-auto modal-scroll grow"
                    style="scrollbar-width: thin;"
                >

                    {{-- SEARCH --}}
                    <div class="relative mb-5">

                        <input
                            type="text"
                            wire:model.live="searchSiswa"
                            placeholder="Cari Nama atau NIS"
                            class="w-full border border-gray-200 rounded-md pl-4 pr-3 py-2 text-[13px] text-gray-700 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary shadow-sm"
                        >

                    </div>


                    {{-- LIST SISWA --}}
                    @php
                        $filteredStudents = collect($students)->filter(function ($student) {
                            $keyword = strtolower(trim($this->searchSiswa));

                            if ($keyword === '') {
                                return true;
                            }

                            return str_contains(
                                strtolower($student->nama ?? ''),
                                $keyword
                            ) || str_contains(
                                strtolower($student->nis ?? ''),
                                $keyword
                            );
                        });
                    @endphp

                    <div class="flex flex-col gap-3">

                        @forelse($filteredStudents as $siswa)

                            <div
                                wire:click="selectStudent({{ $siswa->id }})"
                                class="border border-gray-200 rounded-md p-4 cursor-pointer hover:border-primary hover:bg-bg-light transition-colors {{ $siswa_id == $siswa->id ? 'border-primary bg-bg-light' : '' }}"
                            >

                                <div class="flex items-center gap-3">

                                    <div class="w-10 h-10 bg-icon-bg text-primary rounded-full flex items-center justify-center font-bold text-[13px] shrink-0">
                                        {{ $this->getInitials($siswa->nama ?? '') }}
                                    </div>

                                    <div>

                                        <h4 class="text-[14px] font-bold text-gray-900">
                                            {{ $siswa->nama ?? '-' }}
                                        </h4>

                                        <p class="text-[12px] text-gray-500 mt-1">
                                            NIS: {{ $siswa->nis ?? '-' }}

                                            <span class="ml-2">
                                                Kelas: {{ $siswa->kelas_label ?? '-' }}
                                            </span>

                                            @if($siswa->jurusan_label)
                                                <span class="ml-1">
                                                    {{ $siswa->jurusan_label }}
                                                </span>
                                            @endif

                                        </p>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="p-6 text-center text-gray-500 text-sm">
                                Tidak ada siswa ditemukan.
                            </div>

                        @endforelse

                    </div>

                </div>


                {{-- FOOTER MODAL SISWA --}}
                <div class="bg-bg-light px-6 py-4 border-t border-gray-100 flex justify-end shrink-0">

                    <button
                        type="button"
                        wire:click="closeStudentModal"
                        class="px-5 py-2 bg-white border border-gray-200 rounded-md text-[13px] font-bold text-gray-600 hover:bg-gray-50 transition-colors shadow-sm"
                    >
                        Batal
                    </button>

                </div>

            </div>

        </div>

    </x-shared.modal>

</div>