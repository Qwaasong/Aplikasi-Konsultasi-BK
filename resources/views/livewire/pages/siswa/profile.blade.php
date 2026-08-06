<div class="siswa-wrapper">
    <div class="siswa-container">

        {{-- Top Bar --}}
        <div class="siswa-topbar">
            <div class="siswa-brand">
                <x-atoms.icon variant="student" size="lg" color="#086375" />
                Portal Siswa — Bimbingan Konseling
            </div>
            <div class="siswa-topbar-actions">
                <a href="{{ route('logout') }}"
                   class="inline-flex items-center gap-1.5">
                    <x-atoms.icon variant="logout" size="sm" />
                    <span>Logout</span>
                </a>
            </div>
        </div>

        {{-- Hero Card --}}
        <div class="hero-card">
            <div class="hero-avatar">{{ $siswa->initials }}</div>
            <div class="hero-info">
                <h2>{{ $siswa->nama ?? auth()->user()->nama }}</h2>
                <p>NIS: {{ $siswa->nis ?? '-' }}</p>
                <div class="hero-badges">
                    <span class="hero-badge">
                        {{ $siswa->kelas_label ?? '-' }}
                    </span>
                    @if($siswa->kelas?->jurusan)
                        <span class="hero-badge">{{ $siswa->jurusan_label }}</span>
                    @endif
                    @if($siswa->agama)
                        <span class="hero-badge">{{ $siswa->agama }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="tabs-bar">
            <button class="tab-btn inline-flex items-center justify-center gap-2 {{ $activeTab === 'profil' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'profil')">
                <x-atoms.icon variant="user" size="sm" />
                <span>Data Pribadi</span>
            </button>
            <button class="tab-btn inline-flex items-center justify-center gap-2 {{ $activeTab === 'komulatif' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'komulatif')">
                <x-atoms.icon variant="assessment" size="sm" />
                <span>Kumulatif Record</span>
            </button>
            <button class="tab-btn inline-flex items-center justify-center gap-2 {{ $activeTab === 'password' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'password')">
                <x-atoms.icon variant="lock" size="sm" />
                <span>Ubah Password</span>
            </button>
        </div>

        {{-- ===== TAB: PROFIL ===== --}}
        @if($activeTab === 'profil')
            <div class="card-panel">
                <div class="card-header-section">
                    <div class="card-icon" style="background:#eff6ff;">
                        <x-atoms.icon variant="user" size="md" color="#3b82f6" />
                    </div>
                    <h3>Data Pribadi</h3>
                </div>

                @if(session()->has('status_profil'))
                    <div class="alert-success">
                        ✓ {{ session('status_profil') }}
                    </div>
                @endif

                <form wire:submit="updateProfil">
                    <div class="section-divider"><span>Akun</span></div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label for="p-nama">Nama Lengkap</label>
                            <input type="text" id="p-nama" wire:model="nama" placeholder="Masukkan nama lengkap">
                            @error('nama') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label for="p-email">Email</label>
                            <input type="email" id="p-email" wire:model="email" placeholder="Masukkan email">
                            @error('email') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label for="p-nohp">No. HP</label>
                            <input type="text" id="p-nohp" wire:model="no_hp" placeholder="Masukkan nomor HP">
                            @error('no_hp') <p class="error-msg">{{ $message }}</p> @enderror
                        </div>
                        <div class="form-group">
                            <label for="p-agama">Agama</label>
                            <input type="text" id="p-agama" wire:model="agama" placeholder="Masukkan agama">
                        </div>
                    </div>

                    <div class="section-divider"><span>Data Kelahiran</span></div>
                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label for="p-tmpat">Tempat Lahir</label>
                            <input type="text" id="p-tmpat" wire:model="tempat_lahir" placeholder="Kota lahir">
                        </div>
                        <div class="form-group">
                            <label for="p-tgl">Tanggal Lahir</label>
                            <input type="date" id="p-tgl" wire:model="tgl_lahir">
                        </div>
                        <div class="form-group">
                            <label for="p-asalsmp">Asal SMP</label>
                            <input type="text" id="p-asalsmp" wire:model="asal_smp" placeholder="Nama SMP asal">
                        </div>
                    </div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label for="p-anakke">Anak Ke-</label>
                            <input type="number" id="p-anakke" wire:model="anak_ke" min="1" placeholder="Contoh: 1">
                        </div>
                        <div class="form-group">
                            <label for="p-jumsdr">Jumlah Saudara</label>
                            <input type="number" id="p-jumsdr" wire:model="jml_saudara" min="0" placeholder="Contoh: 2">
                        </div>
                    </div>

                    <div class="section-divider"><span>Minat & Rencana</span></div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label for="p-hobi">Hobi</label>
                            <input type="text" id="p-hobi" wire:model="hobi" placeholder="Contoh: Membaca, Olahraga">
                        </div>
                        <div class="form-group">
                            <label for="p-bakat">Bakat</label>
                            <input type="text" id="p-bakat" wire:model="bakat" placeholder="Contoh: Musik, Desain">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="p-rencana">Rencana Setelah Lulus</label>
                            <input type="text" id="p-rencana" wire:model="rencana_lulus" placeholder="Kuliah, Bekerja, dll.">
                        </div>
                    </div>

                    <div class="section-divider"><span>Alamat</span></div>
                    <div class="form-group">
                        <label for="p-alamat">Alamat Lengkap</label>
                        <textarea id="p-alamat" wire:model="alamat" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                    </div>

                    <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                        <button type="submit" class="save-btn inline-flex items-center gap-2">
                            <span wire:loading wire:target="updateProfil">Menyimpan...</span>
                            <span wire:loading.remove wire:target="updateProfil" class="inline-flex items-center gap-2">
                                <x-atoms.icon variant="file" size="sm" color="white" />
                                <span>Simpan Data Pribadi</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- ===== TAB: KOMULATIF RECORD ===== --}}
        @if($activeTab === 'komulatif')
            <div class="card-panel">
                <div class="card-header-section">
                    <div class="card-icon" style="background:#fef3c7;">
                        <x-atoms.icon variant="assessment" size="md" color="#d97706" />
                    </div>
                    <div>
                        <h3>Kumulatif Record Siswa</h3>
                    </div>
                </div>

                <p style="color:#64748b;font-size:0.85rem;margin:-10px 0 20px;">
                    Formulir ini berisi data keluarga dan kondisi rumah yang membantu guru BK memahami kondisi sosial ekonomi Anda.
                    Data bersifat rahasia dan hanya diakses oleh guru BK Anda.
                </p>

                @if(session()->has('status_keluarga'))
                    <div class="alert-success">✓ {{ session('status_keluarga') }}</div>
                @endif

                <form wire:submit="saveKomulatifRecord">
                    <div class="form-group" style="margin-bottom:16px;">
                        <label for="k-tahun">Tahun Pelajaran</label>
                        <input type="text" id="k-tahun" wire:model="tahun_pelajaran" placeholder="Contoh: 2025/2026">
                    </div>

                    <div class="section-divider"><span>Data Ayah</span></div>
                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label for="k-ayah">Nama Ayah</label>
                            <input type="text" id="k-ayah" wire:model="nama_ayah" placeholder="Nama lengkap ayah">
                        </div>
                        <div class="form-group">
                            <label for="k-pendidayah">Pendidikan Ayah</label>
                            <select id="k-pendidayah" wire:model="pendidikan_ayah">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2/S3">S2/S3</option>
                                <option value="Tidak Tamat SD">Tidak Tamat SD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="k-pkerjayah">Pekerjaan Ayah</label>
                            <input type="text" id="k-pkerjayah" wire:model="pekerjaan_ayah" placeholder="Contoh: Wiraswasta">
                        </div>
                    </div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label for="k-waayah">No WA Ayah</label>
                            <input type="text" id="k-waayah" wire:model="nomor_wa_ayah" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="form-group">
                            <label for="k-alamatayah">Alamat Ayah</label>
                            <input type="text" id="k-alamatayah" wire:model="alamat_ayah" placeholder="Jika berbeda dengan alamat siswa">
                        </div>
                    </div>

                    <div class="section-divider"><span>Data Ibu</span></div>
                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label for="k-ibu">Nama Ibu</label>
                            <input type="text" id="k-ibu" wire:model="nama_ibu" placeholder="Nama lengkap ibu">
                        </div>
                        <div class="form-group">
                            <label for="k-pendibu">Pendidikan Ibu</label>
                            <select id="k-pendibu" wire:model="pendidikan_ibu">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2/S3">S2/S3</option>
                                <option value="Tidak Tamat SD">Tidak Tamat SD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="k-pkeribu">Pekerjaan Ibu</label>
                            <input type="text" id="k-pkeribu" wire:model="pekerjaan_ibu" placeholder="Contoh: Ibu Rumah Tangga">
                        </div>
                    </div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label for="k-waibu">No WA Ibu</label>
                            <input type="text" id="k-waibu" wire:model="nomor_wa_ibu" placeholder="08xxxxxxxxxx">
                        </div>
                        <div class="form-group">
                            <label for="k-alamatibu">Alamat Ibu</label>
                            <input type="text" id="k-alamatibu" wire:model="alamat_ibu" placeholder="Jika berbeda dengan alamat siswa">
                        </div>
                    </div>

                    <div class="section-divider"><span>Kontak Orang Tua</span></div>
                    <div class="form-group">
                        <label for="k-telportu">Telepon Orang Tua</label>
                        <input type="text" id="k-telportu" wire:model="telp_ortu" placeholder="Nomor telepon yang bisa dihubungi">
                    </div>

                    <div class="section-divider"><span>Kondisi Tempat Tinggal</span></div>
                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label for="k-status">Status Rumah</label>
                            <select id="k-status" wire:model="status_rumah">
                                <option value="">Pilih Status</option>
                                <option value="Milik Sendiri">Milik Sendiri</option>
                                <option value="Kontrak/Sewa">Kontrak/Sewa</option>
                                <option value="Numpang">Numpang</option>
                                <option value="Asrama">Asrama</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="k-lokasi">Lokasi Rumah</label>
                            <select id="k-lokasi" wire:model="lokasi_rumah">
                                <option value="">Pilih Lokasi</option>
                                <option value="Perkotaan">Perkotaan</option>
                                <option value="Pinggiran Kota">Pinggiran Kota</option>
                                <option value="Pedesaan">Pedesaan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="k-dinding">Dinding Rumah</label>
                            <select id="k-dinding" wire:model="dinding_rumah">
                                <option value="">Pilih Material</option>
                                <option value="Tembok">Tembok</option>
                                <option value="Kayu">Kayu</option>
                                <option value="Bambu">Bambu</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label for="k-lantai">Lantai Rumah</label>
                            <select id="k-lantai" wire:model="lantai_rumah">
                                <option value="">Pilih Material</option>
                                <option value="Keramik">Keramik</option>
                                <option value="Plester">Plester</option>
                                <option value="Tanah">Tanah</option>
                                <option value="Kayu">Kayu</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="k-jmlkamar">Jumlah Kamar</label>
                            <input type="number" id="k-jmlkamar" wire:model="jml_kamar" min="0" placeholder="Contoh: 3">
                        </div>
                        <div class="form-group">
                            <label for="k-jmltv">Jumlah TV</label>
                            <input type="number" id="k-jmltv" wire:model="jml_tv" min="0" placeholder="Contoh: 1">
                        </div>
                    </div>
                    <div class="form-row">
                        <label class="checkbox-row">
                            <input type="checkbox" wire:model="punya_kamar_sendiri">
                            <label>Memiliki kamar tidur sendiri</label>
                        </label>
                    </div>

                    <div class="section-divider"><span>Kendaraan & Transportasi</span></div>
                    <div class="form-row cols-3">
                        <div class="form-group">
                            <label for="k-mobil">Jumlah Mobil</label>
                            <input type="number" id="k-mobil" wire:model="kendaraan_mobil" min="0" placeholder="0">
                        </div>
                        <div class="form-group">
                            <label for="k-motor">Jumlah Motor</label>
                            <input type="number" id="k-motor" wire:model="kendaraan_motor" min="0" placeholder="0">
                        </div>
                        <div class="form-group">
                            <label for="k-kesekolah">Kendaraan ke Sekolah</label>
                            <select id="k-kesekolah" wire:model="kendaraan_ke_sekolah">
                                <option value="">Pilih Kendaraan</option>
                                <option value="Jalan Kaki">Jalan Kaki</option>
                                <option value="Sepeda">Sepeda</option>
                                <option value="Motor">Motor</option>
                                <option value="Mobil">Mobil</option>
                                <option value="Angkutan Umum">Angkutan Umum</option>
                                <option value="Antar Jemput">Antar Jemput</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-divider"><span>Lainnya</span></div>
                    <div class="form-row cols-2">
                        <div class="form-group">
                            <label for="k-biaya">Biaya Sekolah Dari</label>
                            <select id="k-biaya" wire:model="biaya_sekolah_dari">
                                <option value="">Pilih Sumber</option>
                                <option value="Orang Tua">Orang Tua</option>
                                <option value="Beasiswa">Beasiswa</option>
                                <option value="Sendiri">Sendiri</option>
                                <option value="Saudara">Saudara</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="k-medsos">Media Sosial yang Digunakan</label>
                            <input type="text" id="k-medsos" wire:model="media_sosial" placeholder="Contoh: Instagram, TikTok">
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                        <button type="submit" class="save-btn inline-flex items-center gap-2">
                            <span wire:loading wire:target="saveKomulatifRecord">Menyimpan...</span>
                            <span wire:loading.remove wire:target="saveKomulatifRecord" class="inline-flex items-center gap-2">
                                <x-atoms.icon variant="file" size="sm" color="white" />
                                <span>Simpan Kumulatif Record</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- ===== TAB: PASSWORD ===== --}}
        @if($activeTab === 'password')
            <div class="card-panel" style="max-width:520px;">
                <div class="card-header-section">
                    <div class="card-icon" style="background:#fef2f2;">
                        <x-atoms.icon variant="lock" size="md" color="#ef4444" />
                    </div>
                    <h3>Ubah Password</h3>
                </div>

                @if(session()->has('status_password'))
                    <div class="alert-success">✓ {{ session('status_password') }}</div>
                @endif

                <form wire:submit="updatePassword">
                    <div class="form-group">
                        <label for="pw-current">Password Saat Ini</label>
                        <input type="password" id="pw-current" wire:model="current_password" placeholder="Masukkan password lama">
                        @error('current_password') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="pw-new">Password Baru</label>
                        <input type="password" id="pw-new" wire:model="new_password" placeholder="Minimal 8 karakter">
                        @error('new_password') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                    <div class="form-group">
                        <label for="pw-confirm">Konfirmasi Password Baru</label>
                        <input type="password" id="pw-confirm" wire:model="new_password_confirmation" placeholder="Ulangi password baru">
                    </div>
                    <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                        <button type="submit" class="save-btn inline-flex items-center gap-2">
                            <span wire:loading wire:target="updatePassword">Memperbarui...</span>
                            <span wire:loading.remove wire:target="updatePassword" class="inline-flex items-center gap-2">
                                <x-atoms.icon variant="lock" size="sm" color="white" />
                                <span>Perbarui Password</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>
</div>
