<div class="flex-1 flex flex-col min-w-0 bg-gray-50/50 min-h-screen p-6 lg:p-10">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-teal-50 text-brand-teal border border-teal-100">
                Portal Siswa
            </span>
            <h1 class="text-2xl font-bold text-gray-900 mt-1">
                {{ $siswa->nama ?? auth()->user()->nama }}
            </h1>
        </div>
        <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-100 text-slate-700 text-sm font-medium">
            <x-atoms.icon variant="student" size="sm" color="#086375" />
            <span>{{ $siswa->kelas_label ?? '-' }} {{ $siswa->jurusan_label ? '• '.$siswa->jurusan_label : '' }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[320px_minmax(0,1fr)] gap-8">
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm h-fit">
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-full bg-[#086375] text-white flex items-center justify-center text-2xl font-bold shadow-sm">
                    {{ $siswa->initials }}
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $siswa->nama ?? auth()->user()->nama }}</h2>
                <p class="text-sm text-gray-500">NIS: {{ $siswa->nis ?? '-' }}</p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-teal-50 text-brand-teal border border-teal-100">
                        {{ $siswa->kelas_label ?? '-' }}
                    </span>
                    @if($siswa->kelas?->jurusan)
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $siswa->jurusan_label }}
                        </span>
                    @endif
                    @if($siswa->agama)
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                            {{ $siswa->agama }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white border border-gray-100 rounded-2xl p-3 shadow-sm">
                <div class="flex flex-wrap gap-2">
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition {{ $activeTab === 'profil' ? 'bg-[#086375] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
                            wire:click="$set('activeTab', 'profil')">
                        <x-atoms.icon variant="user" size="sm" color="{{ $activeTab === 'profil' ? '#ffffff' : '#475569' }}" />
                        <span>Data Pribadi</span>
                    </button>
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition {{ $activeTab === 'komulatif' ? 'bg-[#086375] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
                            wire:click="$set('activeTab', 'komulatif')">
                        <x-atoms.icon variant="assessment" size="sm" color="{{ $activeTab === 'komulatif' ? '#ffffff' : '#475569' }}" />
                        <span>Kumulatif Record</span>
                    </button>
                    <button class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition {{ $activeTab === 'password' ? 'bg-[#086375] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}"
                            wire:click="$set('activeTab', 'password')">
                        <x-atoms.icon variant="lock" size="sm" color="{{ $activeTab === 'password' ? '#ffffff' : '#475569' }}" />
                        <span>Ubah Password</span>
                    </button>
                </div>
            </div>

            {{-- ===== TAB: PROFIL ===== --}}
            @if($activeTab === 'profil')
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                        <div class="p-2 rounded-xl bg-blue-50 text-blue-600">
                            <x-atoms.icon variant="user" size="md" color="#2563eb" />
                        </div>
                        <h3 class="font-bold text-gray-900 text-[15px]">Data Pribadi</h3>
                    </div>

                    @if(session()->has('status_profil'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
                            {{ session('status_profil') }}
                        </div>
                    @endif

                    <form wire:submit="updateProfil" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-molecules.input-field label="Nama Lengkap" id="p-nama" type="text" name="nama" wire:model="nama" :error="$errors->first('nama')" />
                            <x-molecules.input-field label="Email" id="p-email" type="email" name="email" wire:model="email" :error="$errors->first('email')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-molecules.input-field label="No. HP" id="p-nohp" type="text" name="no_hp" wire:model="no_hp" :error="$errors->first('no_hp')" />
                            <x-molecules.input-field label="Agama" id="p-agama" type="text" name="agama" wire:model="agama" :error="$errors->first('agama')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-molecules.input-field label="Tempat Lahir" id="p-tmpat" type="text" name="tempat_lahir" wire:model="tempat_lahir" :error="$errors->first('tempat_lahir')" />
                            <x-molecules.input-field label="Tanggal Lahir" id="p-tgl" type="date" name="tgl_lahir" wire:model="tgl_lahir" :error="$errors->first('tgl_lahir')" />
                            <x-molecules.input-field label="Asal SMP" id="p-asalsmp" type="text" name="asal_smp" wire:model="asal_smp" :error="$errors->first('asal_smp')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-molecules.input-field label="Anak Ke-" id="p-anakke" type="number" name="anak_ke" wire:model="anak_ke" :error="$errors->first('anak_ke')" />
                            <x-molecules.input-field label="Jumlah Saudara" id="p-jumsdr" type="number" name="jml_saudara" wire:model="jml_saudara" :error="$errors->first('jml_saudara')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-molecules.input-field label="Hobi" id="p-hobi" type="text" name="hobi" wire:model="hobi" :error="$errors->first('hobi')" />
                            <x-molecules.input-field label="Bakat" id="p-bakat" type="text" name="bakat" wire:model="bakat" :error="$errors->first('bakat')" />
                        </div>

                        <x-molecules.input-field label="Rencana Setelah Lulus" id="p-rencana" type="text" name="rencana_lulus" wire:model="rencana_lulus" :error="$errors->first('rencana_lulus')" />

                        <div>
                            <x-atoms.input-label for="p-alamat" size="md">Alamat Lengkap</x-atoms.input-label>
                            <textarea id="p-alamat" wire:model="alamat" rows="3" class="w-full border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-brand-dark focus:border-brand-dark transition duration-150 px-4 py-2 text-sm" placeholder="Masukkan alamat lengkap"></textarea>
                            @error('alamat')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-2">
                            <x-atoms.button type="submit" variant="primary">
                                Simpan Data Pribadi
                            </x-atoms.button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ===== TAB: KOMULATIF RECORD ===== --}}
            @if($activeTab === 'komulatif')
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                        <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                            <x-atoms.icon variant="assessment" size="md" color="#d97706" />
                        </div>
                        <h3 class="font-bold text-gray-900 text-[15px]">Kumulatif Record Siswa</h3>
                    </div>

                    <p class="text-sm text-slate-600 mb-6">
                        Formulir ini berisi data keluarga dan kondisi rumah yang membantu guru BK memahami kondisi sosial ekonomi Anda. Data bersifat rahasia dan hanya diakses oleh guru BK Anda.
                    </p>

                    @if(session()->has('status_keluarga'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
                            {{ session('status_keluarga') }}
                        </div>
                    @endif

                    <form wire:submit="saveKomulatifRecord" class="space-y-4">
                        <x-molecules.input-field label="Tahun Pelajaran" id="k-tahun" type="text" name="tahun_pelajaran" wire:model="tahun_pelajaran" :error="$errors->first('tahun_pelajaran')" />

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-molecules.input-field label="Nama Ayah" id="k-ayah" type="text" name="nama_ayah" wire:model="nama_ayah" :error="$errors->first('nama_ayah')" />
                            <x-molecules.input-field label="Pendidikan Ayah" id="k-pendidayah" type="text" name="pendidikan_ayah" wire:model="pendidikan_ayah" :error="$errors->first('pendidikan_ayah')" />
                            <x-molecules.input-field label="Pekerjaan Ayah" id="k-pkerjayah" type="text" name="pekerjaan_ayah" wire:model="pekerjaan_ayah" :error="$errors->first('pekerjaan_ayah')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-molecules.input-field label="No WA Ayah" id="k-waayah" type="text" name="nomor_wa_ayah" wire:model="nomor_wa_ayah" :error="$errors->first('nomor_wa_ayah')" />
                            <x-molecules.input-field label="Alamat Ayah" id="k-alamatayah" type="text" name="alamat_ayah" wire:model="alamat_ayah" :error="$errors->first('alamat_ayah')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-molecules.input-field label="Nama Ibu" id="k-ibu" type="text" name="nama_ibu" wire:model="nama_ibu" :error="$errors->first('nama_ibu')" />
                            <x-molecules.input-field label="Pendidikan Ibu" id="k-pendibu" type="text" name="pendidikan_ibu" wire:model="pendidikan_ibu" :error="$errors->first('pendidikan_ibu')" />
                            <x-molecules.input-field label="Pekerjaan Ibu" id="k-pkeribu" type="text" name="pekerjaan_ibu" wire:model="pekerjaan_ibu" :error="$errors->first('pekerjaan_ibu')" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-molecules.input-field label="No WA Ibu" id="k-waibu" type="text" name="nomor_wa_ibu" wire:model="nomor_wa_ibu" :error="$errors->first('nomor_wa_ibu')" />
                            <x-molecules.input-field label="Alamat Ibu" id="k-alamatibu" type="text" name="alamat_ibu" wire:model="alamat_ibu" :error="$errors->first('alamat_ibu')" />
                        </div>

                        <div class="flex justify-end pt-2">
                            <x-atoms.button type="submit" variant="primary">
                                Simpan Kumulatif Record
                            </x-atoms.button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- ===== TAB: PASSWORD ===== --}}
            @if($activeTab === 'password')
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                        <div class="p-2 rounded-xl bg-red-50 text-red-600">
                            <x-atoms.icon variant="lock" size="md" color="#dc2626" />
                        </div>
                        <h3 class="font-bold text-gray-900 text-[15px]">Ubah Password</h3>
                    </div>

                    @if(session()->has('status_password'))
                        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
                            {{ session('status_password') }}
                        </div>
                    @endif

                    <form wire:submit="updatePassword" class="space-y-4">
                        <x-molecules.input-field label="Password Saat Ini" id="current_password" type="password" name="current_password" wire:model="current_password" :error="$errors->first('current_password')" />
                        <x-molecules.input-field label="Password Baru" id="new_password" type="password" name="new_password" wire:model="new_password" :error="$errors->first('new_password')" />
                        <x-molecules.input-field label="Konfirmasi Password Baru" id="new_password_confirmation" type="password" name="new_password_confirmation" wire:model="new_password_confirmation" :error="$errors->first('new_password_confirmation')" />

                        <div class="flex justify-end pt-2">
                            <x-atoms.button type="submit" variant="primary">
                                Perbarui Password
                            </x-atoms.button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
