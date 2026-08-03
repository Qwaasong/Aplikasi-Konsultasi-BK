<div class="siswa-wrapper">
    <div class="siswa-container">

        {{-- Top Bar --}}
        <div class="siswa-topbar">
            <div class="siswa-brand">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:26px;height:26px;">
                    <path d="M12 2L3 7l9 5 9-5-9-5zm0 10.23L3 7.46v7.08c0 3.47 3.88 6.25 9 6.25s9-2.78 9-6.25V7.46l-9 4.77z"/>
                </svg>
                Portal Siswa — Bimbingan Konseling
            </div>
            <div class="siswa-topbar-actions">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('logout-form-siswa').submit();">
                    Logout
                </a>
                <form id="logout-form-siswa" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
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
            <button class="tab-btn {{ $activeTab === 'profil' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'profil')">
                📋 Data Pribadi
            </button>
            <button class="tab-btn {{ $activeTab === 'komulatif' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'komulatif')">
                📁 Kumulatif Record
            </button>
            <button class="tab-btn {{ $activeTab === 'password' ? 'active' : '' }}"
                    wire:click="$set('activeTab', 'password')">
                🔐 Ubah Password
            </button>
        </div>

        {{-- ===== TAB: PROFIL ===== --}}
        @if($activeTab === 'profil')
            <div class="card-panel">
                <div class="card-header-section">
                    <div class="card-icon" style="background:#eff6ff;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#3b82f6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
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
                        <button type="submit" class="save-btn">
                            <span wire:loading wire:target="updateProfil">Menyimpan...</span>
                            <span wire:loading.remove wire:target="updateProfil">💾 Simpan Data Pribadi</span>
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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#d97706">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                        </svg>
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
                        <button type="submit" class="save-btn">
                            <span wire:loading wire:target="saveKomulatifRecord">Menyimpan...</span>
                            <span wire:loading.remove wire:target="saveKomulatifRecord">💾 Simpan Kumulatif Record</span>
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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#ef4444">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
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
                        <button type="submit" class="save-btn">
                            <span wire:loading wire:target="updatePassword">Memperbarui...</span>
                            <span wire:loading.remove wire:target="updatePassword">🔐 Perbarui Password</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

    </div>
</div>
