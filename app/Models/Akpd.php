<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akpd extends Model
{
    use HasFactory;
    protected $table = 'akpds';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'tahun_pelajaran',
        'q01', 'q02', 'q03', 'q04', 'q05',
        'q06', 'q07', 'q08', 'q09', 'q10',
        'q11', 'q12', 'q13', 'q14', 'q15',
        'q16', 'q17', 'q18', 'q19', 'q20',
        'q21', 'q22', 'q23', 'q24', 'q25',
        'q26', 'q27', 'q28', 'q29', 'q30',
        'q31', 'q32', 'q33', 'q34', 'q35',
        'q36', 'q37', 'q38', 'q39', 'q40',
        'q41', 'q42', 'q43', 'q44', 'q45',
        'q46', 'q47', 'q48', 'q49', 'q50',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public const ASPECT_RANGES = [
        'Pribadi'    => [1, 10],
        'Sosial'     => [11, 20],
        'Belajar'    => [21, 30],
        'Karir'      => [31, 40],
        'Kesimpulan' => [41, 50],
    ];

    public const QUESTIONS = [
        1  => 'Saya merasa belum disiplin dalam beribadah pada Tuhan YME',
        2  => 'Saya kadang-kadang berperilaku dan bertutur kata tidak jujur',
        3  => 'Saya kadang-kadang masih suka menyontek pada waktu tes',
        4  => 'Saya merasa belum bisa mengendalikan emosi dengan baik',
        5  => 'Saya belum paham tentang sikap dan perilaku asertif',
        6  => 'Saya belum tahu cara mengenal dan memahami diri sendiri',
        7  => 'Saya belum memahami potensi diri',
        8  => 'Saya belum tahu perubahan dan permasalahan yang terjadi pada masa remaja',
        9  => 'Saya belum mengenal tentang macam-macam kepribadian',
        10 => 'Saya kurang memiliki rasa percaya diri',
        11 => 'Saya kadang kurang menjaga kesehatan diri',
        12 => 'Saya belum tahu ciri-ciri/sifat/perilaku pribadi yang berkarakter',
        13 => 'Saya merasa kurang memiliki tanggung jawab pada diri sendiri',
        14 => 'Saya kesulitan mengatur waktu belajar dan bermain',
        15 => 'Kondisi orang tua saya sedang tidak harmonis',
        16 => 'Saya merasa tidak betah tinggal di rumah sendiri',
        17 => 'Saya mempunyai masalah dengan anggota keluarga di rumah',
        18 => 'Saya belum bisa menjadi pribadi yang mandiri',
        19 => 'Saya sedang memiliki konflik pribadi',
        20 => 'Saya belum memahami tentang norma/cara membangun berkeluarga',
        21 => 'Saya belum banyak mengenal lingkungan sekolah baru',
        22 => 'Saya belum memahami tentang kenakalan remaja',
        23 => 'Saya masih sedikit mengetahui tentang dampak atau bahaya rokok',
        24 => 'Saya belum banyak mengenal tentang perilaku sosial yang bertanggung jawab',
        25 => 'Saya belum tahu tentang bullying dan cara mensikapinya',
        26 => 'Saya sukar bergaul dengan teman-teman di sekolah',
        27 => 'Sering saya dianggap tidak sopan pada orang lain',
        28 => 'Saya kurang memahami dampak dari media sosial',
        29 => 'Saya jarang bermain/berteman di lingkungan tempat saya tinggal',
        30 => 'Saya belum banyak teman atau sahabat',
        31 => 'Saya kurang suka berkomunikasi dengan teman lawan jenis',
        32 => 'Saya belum tahu cara belajar yang baik dan benar di SMK/MAK',
        33 => 'Saya belum tahu cara meraih prestasi di sekolah',
        34 => 'Saya belum paham tentang gaya belajar dan strategi yang sesuai dengannya',
        35 => 'Orang tua saya tidak peduli dengan kegiatan belajar saya',
        36 => 'Saya masih sering menunda-nunda tugas sekolah/pekerjaan rumah (PR)',
        37 => 'Saya merasa kesulitan dalam memahami pelajaran tertentu',
        38 => 'Saya belum tahu cara memanfaatkan sumber belajar',
        39 => 'Saya belajar jika akan ada tes atau ujian saja',
        40 => 'Saya belum tahu tentang struktur kurikulum yang ada di sekolah',
        41 => 'Saya merasa malas belajar dan kalau belajar sering ngantuk',
        42 => 'Saya belum terbiasa belajar bersama atau belajar kelompok',
        43 => 'Saya belum paham cara memilih lembaga bimbingan belajar yang baik',
        44 => 'Saya belum dapat memanfaatkan teknologi informasi untuk belajar',
        45 => 'Saya belum tahu cara memperoleh bantuan pendidikan (beasiswa)',
        46 => 'Saya terpaksa harus bekerja untuk mencukupi kebutuhan hidup',
        47 => 'Saya merasa bingung memilih kegiatan ekstrakurikuler di sekolah',
        48 => 'Saya merasa belum mantap pada pilihan peminatan yang diambil',
        49 => 'Saya merasa belum paham hubungan antara hobi, bakat, minat, kemampuan dan karir',
        50 => 'Saya belum memiliki perencanaan karir masa depan',
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }

    public function aspectAnswers(): array
    {
        $groups = [];

        foreach (self::ASPECT_RANGES as $aspect => [$start, $end]) {
            $answers = [];
            $yaCount = 0;

            foreach (range($start, $end) as $no) {
                $key = 'q' . str_pad((string) $no, 2, '0', STR_PAD_LEFT);
                $answer = $this->{$key} ?? null;

                if ($answer === 'Ya') {
                    $yaCount++;
                }

                $answers[] = [
                    'no'       => $no,
                    'pertanyaan' => self::QUESTIONS[$no] ?? 'Pertanyaan tidak tersedia',
                    'jawaban'  => $answer ?: '-',
                ];
            }

            $groups[] = [
                'aspect'   => $aspect,
                'answers'  => $answers,
                'ya_count' => $yaCount,
                'total'    => $end - $start + 1,
            ];
        }

        return $groups;
    }
}
