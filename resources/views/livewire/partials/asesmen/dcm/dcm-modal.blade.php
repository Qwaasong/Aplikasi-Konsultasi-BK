<div>
    <x-shared.modal name="form-dcm" maxWidth="lg">

        <div class="flex flex-col h-full max-h-[80vh]">

            {{-- =====================================================
                HEADER
            ====================================================== --}}
            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">

                <h2 class="text-base font-bold text-gray-900 leading-tight">
                    {{ $editingId ? 'Edit DCM' : 'Tambah DCM' }}
                </h2>

                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $editingId
                        ? 'Perbarui data Daftar Catatan Masalah'
                        : 'Catat Daftar Catatan Masalah baru'
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
                    STEP 1
                ================================================== --}}
                <div class="{{ $step === 1 ? 'block' : 'hidden' }}">

                    {{-- PROGRESS --}}
                    <div class="mb-6">

                        <p class="text-[14px] font-bold text-primary mb-2.5">
                            Langkah 1 Dari 2
                        </p>

                        <div class="flex gap-2.5">
                            <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>
                            <div class="h-2.5 w-1/2 bg-gray-200/80 rounded-full"></div>
                        </div>

                    </div>


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
                                    class="text-[13px] font-bold text-gray-500 hover:text-gray-800"
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
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0
                                            3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5
                                            0 0 1 14.998 0A17.933 17.933 0 0 1 12
                                            21.75c-2.676 0-5.216-.584-7.499-1.632Z"
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
                                    class="bg-primary hover:bg-primary-hover text-white px-4 py-2 rounded-md text-[13px] font-semibold"
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
                        DAFTAR CEK MASALAH (DCM)
                    ================================================== --}}
                    <div class="mb-6">

                        <x-atoms.input-label size="sm">
                            Daftar Cek Masalah
                        </x-atoms.input-label>

                        <p class="text-[11px] text-gray-400 mt-1.5 mb-3">
                            Centang masalah yang sesuai dengan kondisi siswa. Pilihan bisa lebih dari satu.
                        </p>

                        <div
                            class="border border-gray-200 rounded-md p-4 space-y-6 max-h-[50vh] overflow-y-auto modal-scroll"
                            style="scrollbar-width: thin;"
                        >

                            @foreach(\App\Models\Dcm::SECTIONS as $section => $title)

                                <div>

                                    <p class="text-[13px] font-bold text-gray-800 mb-2">
                                        {{ $section }}. {{ $title }}
                                    </p>

                                    <div class="space-y-1.5">

                                        @foreach(\App\Models\Dcm::QUESTION_GROUPS[$section] as $kode => $pertanyaan)

                                            <label class="flex items-start gap-2.5 cursor-pointer">

                                                <input
                                                    type="checkbox"
                                                    value="{{ $kode }}"
                                                    wire:model="jawaban.{{ $section }}"
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
                        KESIMPULAN
                    ================================================== --}}
                    <div class="mb-6">

                        <x-atoms.input-label for="kesimpulan" size="sm">
                            Kesimpulan
                        </x-atoms.input-label>

                        <textarea
                            id="kesimpulan"
                            wire:model="kesimpulan"
                            rows="4"
                            class="w-full border border-gray-200 rounded-md p-4 text-[14.5px]
                            text-gray-900 placeholder:text-gray-400 focus:outline-none
                            focus:border-primary focus:ring-1 focus:ring-primary
                            resize-none shadow-sm leading-relaxed"
                            placeholder="Tuliskan kesimpulan hasil DCM."
                        ></textarea>

                        @error('kesimpulan')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- =================================================
                        CATATAN
                    ================================================== --}}
                    <div class="mb-6">

                        <x-atoms.input-label for="catatan" size="sm">
                            Catatan
                        </x-atoms.input-label>

                        <textarea
                            id="catatan"
                            wire:model="catatan"
                            rows="4"
                            class="w-full border border-gray-200 rounded-md p-4 text-[14.5px]
                            text-gray-900 placeholder:text-gray-400 focus:outline-none
                            focus:border-primary focus:ring-1 focus:ring-primary
                            resize-none shadow-sm leading-relaxed"
                            placeholder="Tambahkan catatan jika diperlukan."
                        ></textarea>

                        @error('catatan')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>

                </div>


                {{-- =================================================
                    STEP 2 - UPLOAD FILE
                ================================================== --}}
                <div class="{{ $step === 2 ? 'block' : 'hidden' }}">

                    <div class="mb-6">

                        <p class="text-[14px] font-bold text-primary mb-2.5">
                            Langkah 2 Dari 2
                        </p>

                        <div class="flex gap-2.5">

                            <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>

                            <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>

                        </div>

                    </div>


                    {{-- UPLOAD --}}
                    <div class="mb-6">

                        <label class="block text-[14px] font-bold text-gray-700 mb-2">
                            Pilih File Tambahan
                        </label>

                        <div
                            x-data="{ isDropping: false }"
                            x-on:dragover.prevent="isDropping = true"
                            x-on:dragleave.prevent="isDropping = false"
                            x-on:drop.prevent="
                                isDropping = false;
                                $refs.fileInput.files = $event.dataTransfer.files;
                                $refs.fileInput.dispatchEvent(new Event('change'));
                            "
                            x-on:click="$refs.fileInput.click()"
                            class="bg-bg-light border-2 py-16 px-6 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-[#e9f3f5] transition-colors border-dashed rounded-xl"
                            :class="isDropping
                                ? 'bg-[#e9f3f5] border-primary'
                                : 'border-icon-bg/40'"
                        >

                            <input
                                type="file"
                                wire:model="newFiles"
                                multiple
                                x-ref="fileInput"
                                class="hidden"
                            >

                            <div class="w-[84px] h-[84px] bg-icon-bg/90 rounded-full flex items-center justify-center mb-5">

                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="w-10 h-10 text-primary"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5h10.5A2.25 2.25 0 0019.5 17.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5A2.25 2.25 0 006.75 19.5z"
                                    />
                                </svg>

                            </div>

                            <p class="text-[14px] font-bold text-gray-700">
                                Klik untuk memilih file
                            </p>

                            <p class="text-[12px] text-gray-400 mt-1">
                                atau tarik dan lepaskan file di sini
                            </p>

                            <p class="text-[11px] text-gray-400 mt-3">
                                Maksimal 5 file • PDF, JPG, PNG, DOCX • Maks. 12 MB/file
                            </p>

                        </div>

                        @error('newFiles.*')
                            <span class="text-red-500 text-[13px] font-medium mt-1.5 block">
                                {{ $message }}
                            </span>
                        @enderror

                    </div>


                    {{-- FILE BARU --}}
                    @if(count($files) > 0)

                        <div class="mb-6">

                            <p class="text-[14px] font-bold text-gray-700 mb-3">
                                File yang Dipilih
                            </p>

                            <div class="flex flex-col gap-2">

                                @foreach($files as $index => $file)

                                    <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">

                                        <div class="flex items-center gap-3 min-w-0">

                                            <div class="w-9 h-9 bg-icon-bg rounded-lg flex items-center justify-center shrink-0">

                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                    class="w-5 h-5 text-primary"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A2.625 2.625 0 0112 5.625v-1.5A3.375 3.375 0 008.625.75H6.75A3.375 3.375 0 003.375 4.125v15.75A3.375 3.375 0 006.75 23.25h9.375a3.375 3.375 0 003.375-3.375v-5.625z"
                                                    />
                                                </svg>

                                            </div>

                                            <span class="text-[13px] text-gray-700 truncate">
                                                {{ $file->getClientOriginalName() }}
                                            </span>

                                        </div>

                                        <button
                                            type="button"
                                            wire:click="removeFile({{ $index }})"
                                            class="text-gray-400 hover:text-red-500 p-2 shrink-0"
                                        >
                                            &times;
                                        </button>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endif


                    {{-- FILE LAMA --}}
                    @if($editingId && count($existingFiles) > 0)

                        <div class="mb-6">

                            <p class="text-[14px] font-bold text-gray-700 mb-3">
                                File yang Sudah Tersimpan
                            </p>

                            <div class="flex flex-col gap-2">

                                @foreach($existingFiles as $index => $file)

                                    <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">

                                        <span class="text-[13px] text-gray-700 truncate">
                                            {{ basename($file) }}
                                        </span>

                                        <button
                                            type="button"
                                            wire:click="removeExistingFile({{ $index }})"
                                            class="text-gray-400 hover:text-red-500 p-2"
                                        >
                                            &times;
                                        </button>

                                    </div>

                                @endforeach

                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- =====================================================
                FOOTER
            ====================================================== --}}
            <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl">

                {{-- STEP 1 --}}
                <div class="{{ $step === 1 ? 'flex' : 'hidden' }} gap-3">

                    <x-atoms.button
                        variant="secondary"
                        size="md"
                        x-on:click="show = false"
                    >
                        Batal
                    </x-atoms.button>

                    <x-atoms.button wire:click="nextStep">
                        Langkah Terakhir : Upload File
                    </x-atoms.button>

                </div>


                {{-- STEP 2 --}}
                <div class="{{ $step === 2 ? 'flex' : 'hidden' }} gap-3">

                    <x-atoms.button
                        variant="secondary"
                        size="md"
                        wire:click="previousStep"
                    >
                        Kembali
                    </x-atoms.button>

                    <x-atoms.button wire:click="save">
                        {{ $editingId ? 'Perbarui DCM' : 'Simpan DCM' }}
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