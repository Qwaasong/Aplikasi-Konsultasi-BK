<div>

    <x-shared.modal 
        name="form-sosiometri"
        maxWidth="lg"
    >

        <div class="flex flex-col h-full max-h-[80vh]">


            {{-- =====================================================
                HEADER
            ====================================================== --}}

            <div class="bg-bg-light px-6 py-4 border-b border-gray-100 shrink-0">

                <h2 class="text-base font-bold text-gray-900 leading-tight">

                    {{ $editingId 
                        ? 'Edit Sosiometri' 
                        : 'Tambah Sosiometri'
                    }}

                </h2>


                <p class="text-xs text-gray-500 mt-0.5">

                    {{ $editingId
                        ? 'Perbarui instrumen sosiometri siswa'
                        : 'Buat instrumen sosiometri baru untuk siswa'
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


                        <x-atoms.input-label
                            for="siswa_id"
                            size="sm"
                        >

                            Siswa

                        </x-atoms.input-label>



                        @php

                            $selectedStudent = collect($students)
                                ->firstWhere('id',$siswa_id);

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

                                            Kelas
                                            {{ $selectedStudent->kelas_label ?? '-' }}

                                            {{ $selectedStudent->jurusan_label ?? '' }}

                                            - NIS
                                            {{ $selectedStudent->nis ?? '-' }}

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
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0
                                            3.75 3.75 0 0 1 7.5 0ZM4.501
                                            20.118a7.5 7.5 0 0 1 14.998 0A17.933
                                            17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                                        />

                                    </svg>


                                </div>




                                <h3 class="text-[15px] font-bold text-gray-700 mb-1">

                                    Tidak Ada Siswa Yang Dipilih

                                </h3>



                                <p class="text-[13px] text-gray-400 mb-4">

                                    Pilih siswa untuk membuat instrumen sosiometri.

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
                        JUDUL SOSIOMETRI
                    ================================================== --}}

                    <div class="mb-6">


                        <x-atoms.input-label
                            for="judul"
                            size="sm"
                        >

                            Judul Sosiometri

                        </x-atoms.input-label>




                        <x-atoms.text-input

                            id="judul"

                            wire:model.live="judul"

                            placeholder="Contoh: Pemilihan Teman Kelompok"

                        />




                        <p class="text-xs text-gray-400 mt-2">

                            Berikan nama atau tema untuk instrumen sosiometri yang dibuat.

                        </p>




                        @error('judul')

                            <span class="text-red-500 text-[13px] font-medium mt-2 block">

                                {{ $message }}

                            </span>

                        @enderror



                    </div>





                    {{-- =================================================
                        INSTRUKSI
                    ================================================== --}}


                    <div class="mb-6">


                        <x-atoms.input-label
                            for="instruksi"
                            size="sm"
                        >

                            Instruksi Pengisian

                        </x-atoms.input-label>





                        <textarea

                            id="instruksi"

                            wire:model.live="instruksi"

                            rows="5"

                            class="w-full border border-gray-200 rounded-md
                                p-4 text-[14px]
                                text-gray-900
                                placeholder:text-gray-400
                                focus:outline-none
                                focus:border-primary
                                focus:ring-1
                                focus:ring-primary
                                resize-none shadow-sm
                                leading-relaxed"

                            placeholder="Contoh: Pilih teman yang paling nyaman bekerja sama dalam kelompok."

                        ></textarea>





                        <p class="text-xs text-gray-400 mt-2">

                            Instruksi akan ditampilkan kepada siswa saat mengisi sosiometri.

                        </p>




                        @error('instruksi')

                            <span class="text-red-500 text-[13px] font-medium mt-2 block">

                                {{ $message }}

                            </span>

                        @enderror



                    </div>






                    {{-- =================================================
                        JUMLAH PILIHAN
                    ================================================== --}}


                    <div class="bg-bg-light border border-gray-200 rounded-xl p-5 mb-6">



                        <x-atoms.input-label

                            for="jumlah_pilihan"

                            size="sm"

                        >

                            Jumlah Teman Yang Dipilih

                        </x-atoms.input-label>





                        <x-atoms.text-input

                            id="jumlah_pilihan"

                            type="number"

                            min="1"

                            max="10"

                            wire:model.live="jumlah_pilihan"

                        />





                        <div class="flex items-start gap-3 mt-4">


                            <div class="w-9 h-9 rounded-full bg-icon-bg flex items-center justify-center shrink-0">


                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="w-5 h-5 text-primary"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 9v3.75m0 3h.008v.008H12v-.008ZM10.5 3.75h3l7.5 13H3l7.5-13Z"
                                    />

                                </svg>


                            </div>



                            <div>


                                <p class="text-[13px] font-semibold text-gray-700">

                                    Pengaturan Pilihan

                                </p>



                                <p class="text-xs text-gray-400 mt-1 leading-relaxed">

                                    Tentukan jumlah teman yang dapat dipilih oleh siswa.
                                    Nilai maksimal yang diperbolehkan adalah 10 pilihan.

                                </p>


                            </div>


                        </div>





                        @error('jumlah_pilihan')

                            <span class="text-red-500 text-[13px] font-medium mt-2 block">

                                {{ $message }}

                            </span>

                        @enderror



                    </div>

                                    {{-- =================================================
                    STEP 2
                ================================================== --}}

                <div class="{{ $step === 2 ? 'block' : 'hidden' }}">


                    {{-- Progress --}}

                    <div class="mb-6">


                        <p class="text-[14px] font-bold text-primary mb-2.5">

                            Langkah 2 Dari 2

                        </p>




                        <div class="flex gap-2.5">


                            <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>


                            <div class="h-2.5 w-1/2 bg-primary rounded-full"></div>


                        </div>


                    </div>





                    {{-- =================================================
                        RINGKASAN
                    ================================================== --}}


                    <div class="rounded-xl border border-gray-200 overflow-hidden">



                        {{-- HEADER CARD --}}

                        <div class="bg-bg-light px-5 py-4 border-b">


                            <h3 class="text-[15px] font-bold text-gray-900">

                                Ringkasan Sosiometri

                            </h3>



                            <p class="text-xs text-gray-500 mt-1">

                                Pastikan data instrumen sudah benar sebelum disimpan.

                            </p>


                        </div>





                        <div class="divide-y divide-gray-100">





                            {{-- SISWA --}}

                            <div class="flex justify-between items-center px-5 py-4">


                                <span class="text-sm text-gray-500">

                                    Nama Siswa

                                </span>



                                <span class="font-semibold text-gray-900">


                                    {{
                                        optional(
                                            collect($students)
                                            ->firstWhere('id',$siswa_id)
                                        )->nama ?? '-'
                                    }}


                                </span>


                            </div>






                            {{-- JUDUL --}}

                            <div class="flex justify-between items-center px-5 py-4">


                                <span class="text-sm text-gray-500">

                                    Judul

                                </span>



                                <span class="font-semibold text-gray-900">


                                    {{ $judul ?: '-' }}


                                </span>


                            </div>







                            {{-- JUMLAH PILIHAN --}}

                            <div class="flex justify-between items-center px-5 py-4">


                                <span class="text-sm text-gray-500">

                                    Jumlah Pilihan

                                </span>



                                <span class="font-bold text-primary">


                                    {{ $jumlah_pilihan }}

                                    Siswa


                                </span>


                            </div>






                            {{-- INSTRUKSI --}}


                            <div class="px-5 py-4">


                                <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">

                                    Instruksi

                                </p>



                                <p class="text-sm leading-relaxed text-gray-700 whitespace-pre-line">


                                    {{ $instruksi ?: '-' }}


                                </p>


                            </div>




                        </div>


                    </div>


                </div>
                        </div>



            {{-- =====================================================
                FOOTER
            ====================================================== --}}


            <div class="bg-bg-light px-7 py-5 border-t border-gray-100 flex justify-end shrink-0 rounded-b-xl">





                {{-- STEP 1 FOOTER --}}


                <div class="{{ $step === 1 ? 'flex' : 'hidden' }} gap-3">



                    <x-atoms.button

                        variant="secondary"

                        size="md"

                        x-on:click="show = false"

                    >

                        Batal

                    </x-atoms.button>





                    <x-atoms.button

                        wire:click="nextStep"

                    >

                        Lanjut ke Ringkasan

                    </x-atoms.button>



                </div>








                {{-- STEP 2 FOOTER --}}


                <div class="{{ $step === 2 ? 'flex' : 'hidden' }} gap-3">



                    <x-atoms.button

                        variant="secondary"

                        size="md"

                        wire:click="previousStep"

                    >

                        Kembali

                    </x-atoms.button>






                    <x-atoms.button

                        wire:click="save"

                    >


                        {{ $editingId

                            ? 'Perbarui Sosiometri'

                            : 'Simpan Sosiometri'

                        }}



                    </x-atoms.button>



                </div>



            </div>



        </div>


    </x-shared.modal>


</div>