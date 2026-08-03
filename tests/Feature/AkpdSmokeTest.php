<?php

namespace Tests\Feature;

use App\Models\Akpd;
use App\Models\User;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AkpdSmokeTest extends TestCase
{
    public function test_akpd_index_shows_four_columns_and_detail_page_renders(): void
    {
        $konselor = User::where('role', 'guru_bk')->first()
            ?? User::factory()->create(['role' => 'guru_bk']);

        $record = Akpd::with('siswa.user', 'siswa.kelas')->first()
            ?? Akpd::factory()->create();

        $this->actingAs($konselor);

        // Index: cards first, then the 4-column table after choosing a level.
        $tingkat = $record->siswa?->kelas?->tingkat ?? 'X';

        $index = Volt::test('pages.konselor.asesmen.akpd.index')
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
            ->assertSeeHtml('konselor/asesmen/akpd/' . $record->id . '/detail');

        $html = $index->html();
        $this->assertStringNotContainsString('$record->pribadi', $html);
        $this->assertStringNotContainsString('$record->sosial', $html);

        // Detail: renders the record and aspect content.
        Volt::test('pages.konselor.asesmen.akpd.detail', ['id' => $record->id])
            ->assertOk()
            ->assertSee('Detail AKPD')
            ->assertSee('Data Siswa')
            ->assertSee('Pribadi')
            ->assertSee('Sosial')
            ->assertSee('Belajar')
            ->assertSee('Karir')
            ->assertSee('Kesimpulan')
            ->assertSee('Saya merasa belum disiplin dalam beribadah pada Tuhan YME')
            ->assertSee('Saya belum memiliki perencanaan karir masa depan');
    }
}
