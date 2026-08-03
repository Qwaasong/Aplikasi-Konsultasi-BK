<?php

namespace App\Services\Asesmen;

use App\Models\DataSiswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Helper bersama untuk import CSV dari Google Forms.
 *
 * Spreadsheet gform tidak memuat kolom NIS — identitas hanya Nama + Kelas.
 * Bila siswa belum terdaftar, otomatis dibuatkan User (role siswa) + DataSiswa.
 */
class AsesmenImportHelper
{
    /**
     * Cocokkan siswa via nama (users.nama) + kelas (kelas.nama_kelas),
     * case-insensitive. Bila tidak ditemukan dan nama+kelas terisi, buat otomatis.
     */
    public static function resolveSiswa(string $nama, string $kelas): ?DataSiswa
    {
        $nama = trim($nama);
        $kelas = trim($kelas);

        if ($nama === '' || $kelas === '') {
            return null;
        }

        $matched = DataSiswa::query()
            ->whereHas('user', fn ($q) => $q->whereRaw('LOWER(nama) = ?', [mb_strtolower($nama)]))
            ->whereHas('kelas', fn ($q) => $q->whereRaw('LOWER(nama_kelas) = ?', [mb_strtolower($kelas)]))
            ->first();

        if ($matched) {
            return $matched;
        }

        // Fallback: kelas "X RPL" vs "X RPL 1" — cocokkan substring.
        $matched = DataSiswa::query()
            ->whereHas('user', fn ($q) => $q->whereRaw('LOWER(nama) = ?', [mb_strtolower($nama)]))
            ->whereHas('kelas', fn ($q) => $q->whereRaw('LOWER(nama_kelas) LIKE ?', ['%'.mb_strtolower($kelas).'%']))
            ->first();

        if ($matched) {
            return $matched;
        }

        return self::createSiswa($nama, $kelas);
    }

    public static function createSiswa(string $nama, string $kelas, array $extra = []): DataSiswa
    {
        $kelasId = self::resolveKelasId($kelas);
        $nis = self::uniqueNis();

        $user = User::create([
            'nama' => $nama,
            'username' => 'siswa_'.$nis,
            'email' => $nis.'@sekolah.sch.id',
            'jenis_kelamin' => self::normalizeJenisKelamin($extra['jenis_kelamin'] ?? ''),
            'no_hp' => $extra['no_hp'] ?? '-',
            'foto' => $extra['foto'] ?? '',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'status' => 'aktif',
        ]);

        return DataSiswa::create([
            'user_id' => $user->id,
            'nis' => $nis,
            'kelas_id' => $kelasId,
            'alamat' => $extra['alamat'] ?? '',
            'tempat_lahir' => $extra['tempat_lahir'] ?? null,
            'tgl_lahir' => $extra['tgl_lahir'] ?? null,
            'asal_smp' => $extra['asal_smp'] ?? null,
            'agama' => $extra['agama'] ?? null,
        ]);
    }

    public static function resolveKelasId(string $kelas): int
    {
        if ($kelas === '') {
            return 0;
        }

        $exact = Kelas::whereRaw('LOWER(nama_kelas) = ?', [mb_strtolower($kelas)])->first();

        if ($exact) {
            return $exact->id;
        }

        $like = Kelas::whereRaw('LOWER(nama_kelas) LIKE ?', ['%'.mb_strtolower($kelas).'%'])->first();

        return $like?->id ?? 0;
    }

    public static function uniqueNis(): string
    {
        do {
            $nis = (string) random_int(10000, 999999);
        } while (DataSiswa::where('nis', $nis)->exists());

        return $nis;
    }

    public static function normalizeJenisKelamin(string $value): string
    {
        $v = strtolower(trim($value));

        return match (true) {
            in_array($v, ['l', 'lk', 'laki', 'laki-laki', 'laki laki', 'pria', 'man'], true) => 'L',
            in_array($v, ['p', 'pr', 'perempuan', 'wanita', 'w', 'cewek'], true) => 'P',
            default => 'L',
        };
    }

    public static function resolveTahunPelajaran(mixed $value): ?string
    {
        $v = trim((string) ($value ?? ''));

        return $v === '' ? null : $v;
    }

    /**
     * Parse timestamp Google Forms ("01/08/2026 23:44:58") atau format lain
     * menjadi "Y-m-d". Fallback ke $fallback atau hari ini bila tak terbaca.
     */
    public static function parseTimestamp(mixed $value, ?string $fallback = null): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return $fallback ?? now()->format('Y-m-d');
        }

        $s = trim((string) $value);

        foreach (['d/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y', 'Y-m-d H:i:s', 'Y-m-d'] as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $s)->format('Y-m-d');
            } catch (\Throwable) {
                // coba format berikutnya
            }
        }

        if (is_numeric($s)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $s)->format('Y-m-d');
            } catch (\Throwable) {
                // lanjut ke Carbon::parse
            }
        }

        try {
            return Carbon::parse($s)->format('Y-m-d');
        } catch (\Throwable) {
            return $fallback ?? now()->format('Y-m-d');
        }
    }

    /**
     * Pisahkan kolom "Tempat, tanggal lahir" menjadi [tempat_lahir, tgl_lahir].
     * Contoh: "Malang, 15-05-2008" → ["Malang", "2008-05-15"].
     */
    public static function splitTempatTanggalLahir(mixed $value): array
    {
        $s = trim((string) ($value ?? ''));

        if ($s === '') {
            return [null, null];
        }

        $parts = preg_split('/[,-]\s*/', $s, 2);
        $tempat = trim($parts[0] ?? '');
        $tglRaw = trim($parts[1] ?? '');

        $tgl = null;

        if ($tglRaw !== '') {
            foreach (['d-m-Y', 'd/m/Y', 'd-m-y', 'd/m/y', 'j F Y', 'd F Y'] as $fmt) {
                try {
                    $tgl = Carbon::createFromFormat($fmt, $tglRaw)->format('Y-m-d');
                    break;
                } catch (\Throwable) {
                    // coba format berikutnya
                }
            }

            if ($tgl === null) {
                try {
                    $tgl = Carbon::parse($tglRaw)->format('Y-m-d');
                } catch (\Throwable) {
                    $tgl = null;
                }
            }
        }

        return [$tempat !== '' ? $tempat : null, $tgl];
    }

    public static function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }
}
