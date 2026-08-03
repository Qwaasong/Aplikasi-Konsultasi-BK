<?php

namespace App\Services\Asesmen;

use App\Models\DataSiswa;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AsesmenImportHelper
{
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

        $matched = DataSiswa::query()
            ->whereHas('user', fn ($q) => $q->whereRaw('LOWER(nama) = ?', [mb_strtolower($nama)]))
            ->whereHas('kelas', fn ($q) => $q->whereRaw('LOWER(nama_kelas) LIKE ?', ['%'.mb_strtolower($kelas).'%']))
            ->first();

        return $matched ?: self::createSiswa($nama, $kelas);
    }

    public static function createSiswa(string $nama, string $kelas, array $extra = []): DataSiswa
    {
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
            'kelas_id' => self::resolveKelasId($kelas),
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
        $value = strtolower(trim($value));

        return match (true) {
            in_array($value, ['l', 'lk', 'laki', 'laki-laki', 'laki laki', 'pria'], true) => 'L',
            in_array($value, ['p', 'pr', 'perempuan', 'wanita'], true) => 'P',
            default => 'L',
        };
    }

    public static function resolveTahunPelajaran(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    public static function parseTimestamp(mixed $value, ?string $fallback = null): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return $fallback ?? now()->format('Y-m-d');
        }

        $value = trim((string) $value);

        foreach (['d/m/Y H:i:s', 'd/m/Y H:i', 'd/m/Y', 'Y-m-d H:i:s', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable) {
                // coba format berikutnya
            }
        }

        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                // lanjut ke Carbon::parse
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return $fallback ?? now()->format('Y-m-d');
        }
    }

    public static function splitTempatTanggalLahir(mixed $value): array
    {
        $value = trim((string) ($value ?? ''));

        if ($value === '') {
            return [null, null];
        }

        $parts = preg_split('/[,|-]\s*/', $value, 2);
        $tempat = trim($parts[0] ?? '');
        $tanggal = null;

        foreach (['d-m-Y', 'd/m/Y', 'd-m-y', 'd/m/y', 'j F Y', 'd F Y'] as $format) {
            try {
                $tanggal = Carbon::createFromFormat($format, trim($parts[1] ?? ''))->format('Y-m-d');
                break;
            } catch (\Throwable) {
                // coba format berikutnya
            }
        }

        return [$tempat !== '' ? $tempat : null, $tanggal];
    }

    public static function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));
        $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text) ?? '';
        $text = preg_replace('/\s+/u', ' ', $text) ?? '';

        return trim($text);
    }
}
