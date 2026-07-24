<?php

namespace App\Livewire\Konselor\Asesmen\Sosiometri;

use Livewire\Volt\Component;

class Form extends Component
{
    public string $nama = '';
    public string $kelas = '';
    public string $pilihan1 = '';
    public string $pilihan2 = '';
    public string $pilihan3 = '';
    public string $alasan = '';

    public function __construct()
    {
        parent::__construct();
    }
}
