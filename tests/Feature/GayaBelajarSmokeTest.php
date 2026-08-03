<?php

namespace Tests\Feature;

use App\Models\GayaBelajar;
use App\Models\User;
use Livewire\Volt\Volt;
use Tests\TestCase;

class GayaBelajarSmokeTest extends TestCase
{
    public function test_gaya_belajar_index_shows_four_columns_and_detail_page_renders(): void
    {
        $konselor = User::where('role', 'guru_bk')->first()
            ?? User::factory()->create(['role' => 'guru_bk']);

        $record = GayaBelajar::factory()->create();

        $this->actingAs($konselor);

        // Index: cards first, then the 4-column table after choosing a level.
        $tingkat = $record->siswa?->kelas?->tingkat ?? 'X';

        $index = Volt::test('pages.konselor.asesmen.gaya-belajar.index')
            ->assertSee('Pilih Tingkat')
            ->assertSee('Kelas X')
            ->assertSee('Kelas XI')
            ->assertSee('Kelas XII')
            ->assertDontSeeHtml('wire:click="goToDetail(' . $record->id . ')"')
            ->call('pilihTingkat', $tingkat)
            ->assertSee('Tanggal')
            ->assertSee('Siswa')
            ->assertSee('Kelas')
            ->assertSee('Aksi')
            ->assertSeeHtml('wire:click="goToDetail(' . $record->id . ')"')
            ->assertSeeHtml('konselor/asesmen/gaya-belajar/' . $record->id . '/detail');

        $html = $index->html();
        $this->assertStringNotContainsString('$record->visual', $html);
        $this->assertStringNotContainsString('$record->auditori', $html);
        $this->assertStringNotContainsString('$record->kinestetik', $html);

        // Detail: renders the record, the gform question groups, and the faktor fields.
        Volt::test('pages.konselor.asesmen.gaya-belajar.detail', ['id' => $record->id])
            ->assertOk()
            ->assertSee('Detail Gaya Belajar')
            ->assertSee('Data Siswa')
            ->assertSee('Pertanyaan Gaya Belajar')
            ->assertSee('Anda rapi dan teratur')
            ->assertSee('Anda lebih baik mengeja dengan keras daripada menuliskannya')
            ->assertSee('Faktor Penghambat Belajar')
            ->assertSee('Faktor Pendukung Belajar');
    }
}
