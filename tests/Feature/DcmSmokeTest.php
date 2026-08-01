<?php

namespace Tests\Feature;

use App\Models\Dcm;
use App\Models\User;
use Livewire\Volt\Volt;
use Tests\TestCase;

class DcmSmokeTest extends TestCase
{
    public function test_dcm_index_shows_four_columns_and_detail_page_renders(): void
    {
        $konselor = User::where('role', 'guru_bk')->first()
            ?? User::factory()->create(['role' => 'guru_bk']);

        $record = Dcm::factory()->create();

        $this->actingAs($konselor);

        // Index: cards first, then the 4-column table after choosing a level.
        $tingkat = $record->siswa?->kelas?->tingkat ?? 'X';

        $index = Volt::test('pages.konselor.asesmen.dcm.index')
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
            ->assertSeeHtml('konselor/asesmen/dcm/' . $record->id . '/detail');

        $html = $index->html();
        $this->assertStringNotContainsString('$record->masalah_teridentifikasi', $html);
        $this->assertStringNotContainsString('$record->kesimpulan', $html);

        // Detail: renders the record and the Daftar Cek Masalah groups.
        $checkedCode = array_key_first($record->jawaban ?? []) !== null
            ? ($record->jawaban[array_key_first($record->jawaban)][0] ?? 'A01')
            : 'A01';

        Volt::test('pages.konselor.asesmen.dcm.detail', ['id' => $record->id])
            ->assertOk()
            ->assertSee('Detail DCM')
            ->assertSee('Data Siswa')
            ->assertSee('Daftar Cek Masalah')
            ->assertSee('Masalah Kesehatan')
            ->assertSee($checkedCode);
    }
}
