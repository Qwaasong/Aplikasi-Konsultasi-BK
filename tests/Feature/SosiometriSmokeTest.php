<?php

namespace Tests\Feature;

use App\Models\DataSiswa;
use App\Models\Sosiometri;
use App\Models\SosiometriRespon;
use App\Models\User;
use Livewire\Volt\Volt;
use Tests\TestCase;

class SosiometriSmokeTest extends TestCase
{
    public function test_sosiometri_index_shows_columns_and_detail_page_renders(): void
    {
        $konselor = User::where('role', 'guru_bk')->first()
            ?? User::factory()->create(['role' => 'guru_bk']);

        $siswa = DataSiswa::factory()->create();
        $record = Sosiometri::factory()->create(['siswa_id' => $siswa->id]);
        $dipilih = DataSiswa::factory()->create();

        SosiometriRespon::factory()->create([
            'sosiometri_id' => $record->id,
            'siswa_dipilih_id' => $dipilih->id,
            'siswa_pemilih_id' => $siswa->id,
            'pertanyaan' => 'Q1',
        ]);

        $this->actingAs($konselor);

        // Index: cards first, then the table after choosing a level.
        $tingkat = $record->siswa?->kelas?->tingkat ?? 'X';

        $index = Volt::test('pages.konselor.asesmen.sosiometri.index')
            ->assertSee('Pilih Tingkat')
            ->assertSee('Sosiometri Kelas X')
            ->assertSee('Sosiometri Kelas XI')
            ->assertSee('Sosiometri Kelas XII')
            ->assertDontSeeHtml('wire:click="goToDetail(' . $record->id . ')"')
            ->call('pilihTingkat', $tingkat)
            ->assertSee('Tanggal')
            ->assertSee('Siswa')
            ->assertSee('Kelas')
            ->assertSee('Aksi')
            ->assertSeeHtml('wire:click="goToDetail(' . $record->id . ')"')
            ->assertSeeHtml('konselor/asesmen/sosiometri/' . $record->id . '/detail');

        // The step-2 question input uses the student picker, not free-text names.
        $html = $index->html();
        $this->assertStringNotContainsString('pisahkan dengan koma', $html);

        // Detail: renders the record and the question groups.
        Volt::test('pages.konselor.asesmen.sosiometri.detail', ['id' => $record->id])
            ->assertOk()
            ->assertSee('Detail Sosiometri')
            ->assertSee('Data Siswa')
            ->assertSee('Hasil Sosiometri')
            ->assertSee(Sosiometri::PERTANYAAN['Q1'])
            ->assertSee($dipilih->nama);
    }
}
