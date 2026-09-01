<?php

namespace Tests\Feature;

use App\Models\DataSiswa;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response
            ->assertOk()
            ->assertSeeVolt('profile.update-profile-information-form')
            ->assertSeeVolt('profile.update-password-form')
            ->assertSeeVolt('profile.delete-user-form');
    }

    public function test_student_profile_uses_shared_app_layout_with_sidebar(): void
    {
        $user = User::factory()->siswa()->create();
        DataSiswa::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/siswa/profile');

        $response
            ->assertOk()
            ->assertSee('Halaman Siswa')
            ->assertSee('Profil Saya');
    }

    public function test_student_can_submit_attendance_from_siswa_portal(): void
    {
        $user = User::factory()->siswa()->create();
        $siswa = DataSiswa::factory()->create(['user_id' => $user->id]);
        TahunAjaran::factory()->create(['status_aktif' => true, 'tahun' => '2026/2027', 'semester' => 'Ganjil']);

        $this->actingAs($user);

        $component = Volt::test('pages.siswa.absensi')
            ->set('tanggal', '2026-08-30')
            ->set('status', 'Hadir')
            ->call('saveAbsensi');

        $component->assertHasNoErrors();

        $this->assertDatabaseHas('kehadiran', [
            'siswa_id' => $siswa->id,
            'status' => 'Hadir',
            'tanggal_kehadiran' => '2026-08-30',
        ]);
    }

    public function test_student_attendance_page_lists_recent_history(): void
    {
        $user = User::factory()->siswa()->create();
        $siswa = DataSiswa::factory()->create(['user_id' => $user->id]);
        $tahunAjaran = TahunAjaran::factory()->create(['status_aktif' => true, 'tahun' => '2026/2027', 'semester' => 'Ganjil']);

        Kehadiran::factory()->create([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAjaran->id,
            'tanggal_kehadiran' => '2026-08-28',
            'status' => 'Izin',
        ]);

        $this->actingAs($user);

        $response = $this->get('/siswa/absensi');

        $response
            ->assertOk()
            ->assertSee('Histori Absensi')
            ->assertSee('2026-08-28')
            ->assertSee('Izin');
    }

    public function test_student_can_choose_their_own_class_from_profile(): void
    {
        $user = User::factory()->siswa()->create();
        $kelasA = Kelas::factory()->create(['nama_kelas' => 'X-1', 'tingkat' => 10]);
        $kelasB = Kelas::factory()->create(['nama_kelas' => 'XI-2', 'tingkat' => 11]);
        $siswa = DataSiswa::factory()->create(['user_id' => $user->id, 'kelas_id' => null]);

        $this->actingAs($user);

        $component = Volt::test('pages.siswa.profile')
            ->set('kelas_id', $kelasB->id)
            ->call('updateProfil');

        $component->assertHasNoErrors();
        $siswa->refresh();

        $this->assertSame($kelasB->id, $siswa->kelas_id);
    }

    public function test_student_assessment_page_shows_cards_without_landing_redirect(): void
    {
        $user = User::factory()->siswa()->create();
        DataSiswa::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/siswa/asesmen');

        $response
            ->assertOk()
            ->assertSee('AKPD')
            ->assertSee('https://forms.gle/EiEaJS2VYU6k6AeV8')
            ->assertSee('Kelas XI')
            ->assertSee('https://forms.gle/xNyicyELono4yn9Z7')
            ->assertSee('Kelas XII')
            ->assertSee('https://forms.gle/s5K1thgso3C673DS6')
            ->assertDontSee('/asesmen/akpd');
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-information-form')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->call('updateProfileInformation');

        $component
            ->assertHasNoErrors()
            ->assertNoRedirect();

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.update-profile-information-form')
            ->set('name', 'Test User')
            ->set('email', $user->email)
            ->call('updateProfileInformation');

        $component
            ->assertHasNoErrors()
            ->assertNoRedirect();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'password')
            ->call('deleteUser');

        $component
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Volt::test('profile.delete-user-form')
            ->set('password', 'wrong-password')
            ->call('deleteUser');

        $component
            ->assertHasErrors('password')
            ->assertNoRedirect();

        $this->assertNotNull($user->fresh());
    }
}
