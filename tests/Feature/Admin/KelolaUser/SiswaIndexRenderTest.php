<?php

namespace Tests\Feature\Admin\KelolaUser;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaIndexRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_siswa_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.kelola-user.siswa.index'))
            ->assertOk()
            ->assertSee('Tambah User Siswa')
            ->assertSee('Catat data siswa baru')
            ->assertSee('modal-scroll')
            ->assertSee('Nama <span class="text-red-500">*</span>', false)
            ->assertSee('Kelas <span class="text-red-500">*</span>', false);
    }
}
