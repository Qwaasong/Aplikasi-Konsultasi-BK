<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GayaBelajar extends Model
{
    use HasFactory;
    protected $table = 'gaya_belajars';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'visual',
        'auditori',
        'kinestetik',
        'hasil',
        'catatan',
        'faktor_penghambat',
        'faktor_pendukung',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'visual' => 'integer',
        'auditori' => 'integer',
        'kinestetik' => 'integer',
    ];

    public const QUESTION_GROUPS = [
        'Visual' => [
            'Anda rapi dan teratur',
            'Anda berbicara dengan cepat',
            'Anda perencana dan pengatur jangka panjang yang baik',
            'Anda dapat mengeja dengan baik',
            'Anda sering memikirkan apa yang ingin anda ucapkan',
            'Anda lebih ingat apa yang dilihat daripada yang didengar',
            'Anda mudah menghafal dengan apa yang dilihat',
            'Anda sulit mengingat perintah lisan kecuali jika dituliskan',
            'Anda sering meminta orang mengulang ucapanya kembali',
            'Anda lebih suka membaca daripada dibacakan',
            'Anda suka mencorat coret selama berbicara dengan orang lain',
            'Anda lebih suka melakukan praktek daripada berpidato',
            'Anda lebih menyukai seni rupa daripada seni musik',
            'Anda tahu apa yang harus dikatakan, tetapi tidak berfikir kata yang tepat untuk diucapkan',
        ],
        'Auditorial' => [
            'Anda berbicara kepada diri sendiri saat sedang bekerja',
            'Anda mudah terganggu keributan',
            'Anda menggerakan bibir/melafalkan kata saat membaca',
            'Anda suka membaca keras-keras dan mendengarkan',
            'Anda dapat mengulang dan menirukan nada perubahan dan warna suara',
            'Anda merasa menulis itu sulit, tetapi pandai bercerita',
            'Anda berbicara dengan pola berirama',
            'Anda adalah pembicara yang fasih',
            'Anda lebih menyukai seni musik daripada seni rupa',
            'Anda lebih mudah belajar melalui mendengar daripada melihat',
            'Anda banyak berbicara, suka berdiskusi dan menjelaskan panjang lebar',
            'Anda lebih baik mengeja dengan keras daripada menuliskannya',
        ],
        'Kinestetik' => [
            'Anda berbicara dengan lambat',
            'Anda menyentuh orang untuk mendapatkan perhatiannya',
            'Anda berdiri dekat-dekat saat berbicara dengan seseorang',
            'Anda berorientasi pada fisik dan banyak bergerak',
            'Anda suka belajar dengan praktik',
            'Anda menghafal dengan berjalan',
            'Anda menggunakan jari untuk menunjuk saat membaca',
            'Anda banyak menggunakan isyarat tubuh',
            'Anda tidak bisa duduk tenang untuk waktu yang lama',
            'Anda membuat keputusan berdasarkan perasaan',
            'Anda mengetuk pena atau kaki saat mendengarkan',
            'Anda meluangkan waktu untuk berolahraga dan kegiatan fisik lainnya',
        ],
    ];

    public function siswa()
    {
        return $this->belongsTo(DataSiswa::class, 'siswa_id');
    }

    public function questionGroups(): array
    {
        $attributes = [
            'Visual' => 'visual',
            'Auditorial' => 'auditori',
            'Kinestetik' => 'kinestetik',
        ];

        return collect(self::QUESTION_GROUPS)
            ->map(fn (array $questions, string $name) => [
                'name' => $name,
                'score' => (int) $this->{$attributes[$name]},
                'questions' => $questions,
                'total' => count($questions),
            ])
            ->values()
            ->all();
    }
}
