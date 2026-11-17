<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $override = []): array
    {
        return array_merge([
            'name' => 'Pengguna Baru',
            'email' => 'baru@resper.test',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ], $override);
    }

    public function test_halaman_register_bisa_dibuka_tamu(): void
    {
        $this->get(route('register'))->assertOk()->assertSee('Daftar');
    }

    public function test_user_login_dialihkan_dari_halaman_register(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('register'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_registrasi_membuat_akun_staff_aktif_dan_mengarahkan_ke_login(): void
    {
        $this->post(route('register.store'), $this->payload())
            ->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $user = User::where('email', 'baru@resper.test')->first();

        $this->assertNotNull($user);
        $this->assertSame('081234567890', $user->phone);
        $this->assertSame(UserRole::Staff, $user->role);
        $this->assertSame(AccountStatus::Active, $user->account_status);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
        $this->assertGuest();
    }

    public function test_registrasi_tidak_bisa_menyusupkan_role_manajer(): void
    {
        $this->post(route('register.store'), $this->payload(['role' => 'manager']));

        $this->assertSame(UserRole::Staff, User::where('email', 'baru@resper.test')->first()->role);
    }

    public function test_registrasi_menolak_email_terdaftar(): void
    {
        User::factory()->create(['email' => 'baru@resper.test']);

        $this->post(route('register.store'), $this->payload())->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_registrasi_memvalidasi_input(): void
    {
        $this->post(route('register.store'), $this->payload([
            'name' => '',
            'email' => 'bukan-email',
            'phone' => 'abc',
            'password' => 'pendek',
            'password_confirmation' => 'beda',
        ]))->assertSessionHasErrors(['name', 'email', 'phone', 'password']);

        $this->assertDatabaseCount('users', 0);
    }
}
