<?php

namespace App\Constants;

class GlobalMessages
{
    // Success Messages
    public const SUCCESS_SAVE = 'Data berhasil disimpan!';
    public const SUCCESS_UPDATE = 'Data berhasil diperbarui!';
    public const SUCCESS_DELETE = 'Data berhasil dihapus!';

    // Error Messages
    public const ERROR_GENERIC = 'Terjadi kesalahan sistem, silakan coba lagi nanti.';
    public const ERROR_UNAUTHORIZED = 'Anda tidak memiliki hak akses untuk aksi ini.';
    public const ERROR_NOT_FOUND = 'Data tidak ditemukan.';
    public const ERROR_VALIDATION = 'Terdapat data yang tidak valid, mohon periksa kembali.';
}
