<?php

namespace Tests\Feature;

use App\Models\Peminatan;
use App\Models\User;
use Livewire\Volt\Volt;
use Tests\TestCase;

class TesBakatMinatSmokeTest extends TestCase
{
    public function test_tes_bakat_minat_index_shows_columns_and_detail_page_renders(): void
    {
        $konselor = User::where('role', 'guru_bk')->first()
            ?? User::factory()->create(['role' => 'guru_bk']);

        $record = Peminatan::factory()->create();

        $this->actingAs($konselor);

        $tingkat = $record->siswa?->kelas?->tingkat ?? 'X';

        $index = Volt::test('pages.konselor.asesmen.tes-bakat-minat.index')
            ->assertSee('Pilih Tingkat')
            ->assertSee('Tes Bakat Minat Kelas X')
            ->assertSee('Tes Bakat Minat Kelas XI')
            ->assertSee('Tes Bakat Minat Kelas XII')
            ->assertDontSeeHtml('wire:click="goToDetail(' . $record->id . ')"')
            ->call('pilihTingkat', $tingkat)
            ->assertSee('Tanggal')
            ->assertSee('Siswa')
            ->assertSee('Kelas')
            ->assertSee('Aksi')
            ->assertSeeHtml('wire:click="goToDetail(' . $record->id . ')"')
            ->assertSeeHtml('konselor/asesmen/tes-bakat-minat/' . $record->id . '/detail');

        $html = $index->html();
        $this->assertStringNotContainsString('$record->dominantIntelligences', $html);
        $this->assertStringNotContainsString('$record->pilihan1', $html);

        // Detail: renders the record and the 8 intelligences breakdown.
        Volt::test('pages.konselor.asesmen.tes-bakat-minat.detail', ['id' => $record->id])
            ->assertOk()
            ->assertSee('Pilihan Bakat Minat')
            ->assertSee('Hasil Tes Bakat Minat')
            ->assertSee('Rincian 8 Kecerdasan')
            ->assertSee('Linguistik')
            ->assertSee('Logis-Matematik')
            ->assertSee('Visual-Spasial')
            ->assertSee('Musikal')
            ->assertSee('Interpersonal')
            ->assertSee('Intrapersonal')
            ->assertSee('Kinestetik')
            ->assertSee('Naturalis');
    }
}
