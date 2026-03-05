<?php

namespace App\Constants;

class KonsultasiMessages
{
    // Success Messages
    public const SUCCESS_SCHEDULED = 'Jadwal konsultasi berhasil dibuat!';
    public const SUCCESS_COMPLETED = 'Sesi konsultasi telah ditandai sebagai selesai.';
    public const SUCCESS_APPROVED = 'Permintaan konsultasi telah disetujui.';

    // Error Messages
    public const ERROR_SCHEDULE_CLASH = 'Maaf, jadwal yang dipilih sudah terisi oleh konsultasi lain.';
    public const ERROR_PAST_DATE = 'Tidak dapat membuat jadwal di tanggal yang sudah lewat.';
    public const ERROR_LIMIT_REACHED = 'Anda sudah mencapai batas maksimal pengajuan konsultasi hari ini.';
    public const ERROR_STUDENT_NOT_FOUND = 'Data siswa tidak ditemukan dalam sistem.';
}
