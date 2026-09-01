<?php

namespace App\Livewire\Pages\Siswa;

use App\Models\DataSiswa;
use App\Models\Kelas;
use App\Models\KeluargaSiswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Profil Siswa - Bimbingan Konseling'])]
class Profile extends Component
{
    public ?DataSiswa $siswa = null;
    public ?KeluargaSiswa $keluarga = null;

    public string $activeTab = 'profil';

    // Data Pribadi
    public string $nama = '';
    public string $email = '';
    public string $no_hp = '';
    public ?int $kelas_id = null;
    public string $alamat = '';
    public string $tempat_lahir = '';
    public string $tgl_lahir = '';
    public int|string $anak_ke = '';
    public int|string $jml_saudara = '';
    public string $asal_smp = '';
    public string $agama = '';
    public string $hobi = '';
    public string $bakat = '';
    public string $rencana_lulus = '';

    // Komulatif Record
    public string $tahun_pelajaran = '';
    public string $nama_ayah = '';
    public string $nama_ibu = '';
    public string $pendidikan_ayah = '';
    public string $pendidikan_ibu = '';
    public string $pekerjaan_ayah = '';
    public string $pekerjaan_ibu = '';
    public string $telp_ortu = '';
    public string $alamat_ayah = '';
    public string $alamat_ibu = '';
    public string $nomor_wa_ayah = '';
    public string $nomor_wa_ibu = '';
    public string $status_rumah = '';
    public string $lokasi_rumah = '';
    public string $dinding_rumah = '';
    public string $lantai_rumah = '';
    public int|string $jml_kamar = '';
    public bool $punya_kamar_sendiri = false;
    public int|string $jml_tv = '';
    public int|string $kendaraan_mobil = '';
    public int|string $kendaraan_motor = '';
    public string $biaya_sekolah_dari = '';
    public string $kendaraan_ke_sekolah = '';
    public string $media_sosial = '';

    // Password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = Auth::user();
        $this->siswa = DataSiswa::with(['user', 'kelas.jurusan', 'keluarga'])
            ->where('user_id', $user->id)
            ->first();

        if (!$this->siswa) {
            abort(403, 'Data siswa tidak ditemukan.');
        }

        $this->nama = $user->nama ?? '';
        $this->email = $user->email ?? '';
        $this->no_hp = $user->no_hp ?? '';
        $this->kelas_id = $this->siswa->kelas_id;
        $this->alamat = $this->siswa->alamat ?? '';
        $this->tempat_lahir = $this->siswa->tempat_lahir ?? '';
        $this->tgl_lahir = $this->siswa->tgl_lahir?->format('Y-m-d') ?? '';
        $this->anak_ke = $this->siswa->anak_ke ?? '';
        $this->jml_saudara = $this->siswa->jml_saudara !== null ? $this->siswa->jml_saudara : '';
        $this->asal_smp = $this->siswa->asal_smp ?? '';
        $this->agama = $this->siswa->agama ?? '';
        $this->hobi = $this->siswa->hobi ?? '';
        $this->bakat = $this->siswa->bakat ?? '';
        $this->rencana_lulus = $this->siswa->rencana_lulus ?? '';

        $this->keluarga = $this->siswa->keluarga;
        if ($this->keluarga) {
            $this->tahun_pelajaran = $this->keluarga->tahun_pelajaran ?? '';
            $this->nama_ayah = $this->keluarga->nama_ayah ?? '';
            $this->nama_ibu = $this->keluarga->nama_ibu ?? '';
            $this->pendidikan_ayah = $this->keluarga->pendidikan_ayah ?? '';
            $this->pendidikan_ibu = $this->keluarga->pendidikan_ibu ?? '';
            $this->pekerjaan_ayah = $this->keluarga->pekerjaan_ayah ?? '';
            $this->pekerjaan_ibu = $this->keluarga->pekerjaan_ibu ?? '';
            $this->telp_ortu = $this->keluarga->telp_ortu ?? '';
            $this->alamat_ayah = $this->keluarga->alamat_ayah ?? '';
            $this->alamat_ibu = $this->keluarga->alamat_ibu ?? '';
            $this->nomor_wa_ayah = $this->keluarga->nomor_wa_ayah ?? '';
            $this->nomor_wa_ibu = $this->keluarga->nomor_wa_ibu ?? '';
            $this->status_rumah = $this->keluarga->status_rumah ?? '';
            $this->lokasi_rumah = $this->keluarga->lokasi_rumah ?? '';
            $this->dinding_rumah = $this->keluarga->dinding_rumah ?? '';
            $this->lantai_rumah = $this->keluarga->lantai_rumah ?? '';
            $this->jml_kamar = $this->keluarga->jml_kamar !== null ? $this->keluarga->jml_kamar : '';
            $this->punya_kamar_sendiri = (bool)($this->keluarga->punya_kamar_sendiri ?? false);
            $this->jml_tv = $this->keluarga->jml_tv !== null ? $this->keluarga->jml_tv : '';
            $this->kendaraan_mobil = $this->keluarga->kendaraan_mobil !== null ? $this->keluarga->kendaraan_mobil : '';
            $this->kendaraan_motor = $this->keluarga->kendaraan_motor !== null ? $this->keluarga->kendaraan_motor : '';
            $this->biaya_sekolah_dari = $this->keluarga->biaya_sekolah_dari ?? '';
            $this->kendaraan_ke_sekolah = $this->keluarga->kendaraan_ke_sekolah ?? '';
            $this->media_sosial = $this->keluarga->media_sosial ?? '';
        }
    }

    public function getKelasOptions(): Collection
    {
        return Kelas::orderBy('nama_kelas')->get();
    }

    public function getRencanaLulusOptions(): array
    {
        return ['Bekerja', 'Kuliah', 'Menikah'];
    }

    public function updateProfil(): void
    {
        $user = Auth::user();

        $this->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'alamat' => ['nullable', 'string'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tgl_lahir' => ['nullable', 'date'],
            'anak_ke' => ['nullable', 'integer', 'min:1'],
            'jml_saudara' => ['nullable', 'integer', 'min:0'],
            'asal_smp' => ['nullable', 'string', 'max:200'],
            'agama' => ['nullable', 'string', 'max:50'],
            'hobi' => ['nullable', 'string', 'max:200'],
            'bakat' => ['nullable', 'string', 'max:200'],
            'rencana_lulus' => ['nullable', 'in:Bekerja,Kuliah,Menikah'],
        ]);

        $user->update([
            'nama' => $this->nama,
            'email' => $this->email,
            'no_hp' => $this->no_hp,
        ]);

        $this->siswa->update([
            'kelas_id' => $this->kelas_id,
            'alamat' => $this->alamat,
            'tempat_lahir' => $this->tempat_lahir,
            'tgl_lahir' => $this->tgl_lahir ?: null,
            'anak_ke' => $this->anak_ke !== '' ? $this->anak_ke : null,
            'jml_saudara' => $this->jml_saudara !== '' ? $this->jml_saudara : null,
            'asal_smp' => $this->asal_smp,
            'agama' => $this->agama,
            'hobi' => $this->hobi,
            'bakat' => $this->bakat,
            'rencana_lulus' => $this->rencana_lulus,
        ]);

        session()->flash('status_profil', 'Data profil berhasil disimpan!');
    }

    public function saveKomulatifRecord(): void
    {
        $this->validate([
            'tahun_pelajaran' => ['nullable', 'string', 'max:20'],
            'nama_ayah' => ['nullable', 'string', 'max:150'],
            'nama_ibu' => ['nullable', 'string', 'max:150'],
            'pendidikan_ayah' => ['nullable', 'string', 'max:50'],
            'pendidikan_ibu' => ['nullable', 'string', 'max:50'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:100'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:100'],
            'telp_ortu' => ['nullable', 'string', 'max:20'],
            'alamat_ayah' => ['nullable', 'string'],
            'alamat_ibu' => ['nullable', 'string'],
            'nomor_wa_ayah' => ['nullable', 'string', 'max:20'],
            'nomor_wa_ibu' => ['nullable', 'string', 'max:20'],
            'status_rumah' => ['nullable', 'string', 'max:50'],
            'lokasi_rumah' => ['nullable', 'string', 'max:100'],
            'dinding_rumah' => ['nullable', 'string', 'max:50'],
            'lantai_rumah' => ['nullable', 'string', 'max:50'],
            'jml_kamar' => ['nullable', 'integer', 'min:0'],
            'punya_kamar_sendiri' => ['boolean'],
            'jml_tv' => ['nullable', 'integer', 'min:0'],
            'kendaraan_mobil' => ['nullable', 'integer', 'min:0'],
            'kendaraan_motor' => ['nullable', 'integer', 'min:0'],
            'biaya_sekolah_dari' => ['nullable', 'string', 'max:100'],
            'kendaraan_ke_sekolah' => ['nullable', 'string', 'max:100'],
            'media_sosial' => ['nullable', 'string', 'max:200'],
        ]);

        KeluargaSiswa::updateOrCreate(
            ['siswa_id' => $this->siswa->id],
            [
                'tahun_pelajaran' => $this->tahun_pelajaran,
                'nama_ayah' => $this->nama_ayah,
                'nama_ibu' => $this->nama_ibu,
                'pendidikan_ayah' => $this->pendidikan_ayah,
                'pendidikan_ibu' => $this->pendidikan_ibu,
                'pekerjaan_ayah' => $this->pekerjaan_ayah,
                'pekerjaan_ibu' => $this->pekerjaan_ibu,
                'telp_ortu' => $this->telp_ortu,
                'alamat_ayah' => $this->alamat_ayah,
                'alamat_ibu' => $this->alamat_ibu,
                'nomor_wa_ayah' => $this->nomor_wa_ayah,
                'nomor_wa_ibu' => $this->nomor_wa_ibu,
                'status_rumah' => $this->status_rumah,
                'lokasi_rumah' => $this->lokasi_rumah,
                'dinding_rumah' => $this->dinding_rumah,
                'lantai_rumah' => $this->lantai_rumah,
                'jml_kamar' => $this->jml_kamar !== '' ? $this->jml_kamar : null,
                'punya_kamar_sendiri' => $this->punya_kamar_sendiri,
                'jml_tv' => $this->jml_tv !== '' ? $this->jml_tv : null,
                'kendaraan_mobil' => $this->kendaraan_mobil !== '' ? $this->kendaraan_mobil : null,
                'kendaraan_motor' => $this->kendaraan_motor !== '' ? $this->kendaraan_motor : null,
                'biaya_sekolah_dari' => $this->biaya_sekolah_dari,
                'kendaraan_ke_sekolah' => $this->kendaraan_ke_sekolah,
                'media_sosial' => $this->media_sosial,
            ]
        );

        session()->flash('status_keluarga', 'Kumulatif Record berhasil disimpan!');
    }

    public function updatePassword(): void
    {
        $user = Auth::user();
        $this->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user->update(['password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('status_password', 'Password berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.pages.siswa.profile');
    }
}
