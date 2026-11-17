<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_registrasi_default_menjadi_staff_aktif(): void
    {
        $user = User::create([
            'name' => 'Baru',
            'email' => 'baru@resper.test',
            'password' => 'password',
            'role' => 'manager', // dicoba disusupkan, harus diabaikan
        ]);

        $this->assertSame(UserRole::Staff, $user->fresh()->role);
        $this->assertSame(AccountStatus::Active, $user->fresh()->account_status);
    }

    public function test_dashboard_mengarahkan_ke_halaman_awal_sesuai_role(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('dashboard'))->assertRedirect(route('finance.report'));

        $this->actingAs(User::factory()->cashier()->create())
            ->get(route('dashboard'))->assertRedirect(route('order.pesanan'));

        $this->actingAs(User::factory()->staff()->create())
            ->get(route('dashboard'))->assertRedirect(route('staff.schedule'));
    }

    public function test_staff_hanya_bisa_membuka_jadwal(): void
    {
        $this->actingAs(User::factory()->staff()->create());

        $this->get(route('staff.schedule'))->assertOk()->assertDontSee('+ Add Schedule');
        $this->get(route('staff.index'))->assertForbidden();
        $this->get(route('inventory.index'))->assertForbidden();
        $this->get(route('order.pesanan'))->assertForbidden();
        $this->get(route('finance.report'))->assertForbidden();
        $this->get(route('akun.role.index'))->assertForbidden();
    }

    public function test_kasir_bisa_membuka_pemesanan_tetapi_tidak_inventaris(): void
    {
        $this->actingAs(User::factory()->cashier()->create());

        $this->get(route('order.pesanan'))->assertOk();
        $this->get(route('order.delivery'))->assertOk();
        $this->get(route('inventory.index'))->assertForbidden();
        $this->get(route('finance.report'))->assertForbidden();
    }

    public function test_manajer_bisa_membuka_halaman_kelola_role(): void
    {
        $this->actingAs(User::factory()->manager()->create())
            ->get(route('akun.role.index'))
            ->assertOk()
            ->assertSee('Kelola Role Pengguna');
    }

    public function test_manajer_bisa_mengubah_role_kasir_menjadi_staff(): void
    {
        $manager = User::factory()->manager()->create();
        $cashier = User::factory()->cashier()->create();

        $this->actingAs($manager)
            ->patch(route('akun.role.update', $cashier), [
                'role' => 'staff',
                'account_status' => 'active',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(UserRole::Staff, $cashier->fresh()->role);
    }

    public function test_manajer_tidak_bisa_mengubah_role_sendiri(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->patch(route('akun.role.update', $manager), [
                'role' => 'staff',
                'account_status' => 'active',
            ])
            ->assertSessionHasErrors('role');

        $this->assertSame(UserRole::Manager, $manager->fresh()->role);
    }

    public function test_memulihkan_akun_terblokir_mereset_percobaan_gagal(): void
    {
        $manager = User::factory()->manager()->create();
        $blocked = User::factory()->blocked()->create(['failed_login_attempts' => 5]);

        $this->actingAs($manager)->patch(route('akun.role.update', $blocked), [
            'role' => 'staff',
            'account_status' => 'active',
        ]);

        $this->assertSame(AccountStatus::Active, $blocked->fresh()->account_status);
        $this->assertSame(0, $blocked->fresh()->failed_login_attempts);
    }

    public function test_lima_kali_salah_password_memblokir_akun(): void
    {
        $user = User::factory()->create(['email' => 'kasir@resper.test']);

        for ($i = 0; $i < User::MAX_FAILED_LOGIN; $i++) {
            $this->post(route('login.store'), ['email' => 'kasir@resper.test', 'password' => 'salah']);
        }

        $this->assertSame(AccountStatus::Blocked, $user->fresh()->account_status);

        // Password benar pun ditolak setelah diblokir
        $this->post(route('login.store'), ['email' => 'kasir@resper.test', 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_berhasil_mereset_hitungan_gagal(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@resper.test',
            'failed_login_attempts' => 3,
        ]);

        $this->post(route('login.store'), ['email' => 'staff@resper.test', 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertSame(0, $user->fresh()->failed_login_attempts);
    }

    public function test_user_terblokir_dikeluarkan_dari_sesi(): void
    {
        $this->actingAs(User::factory()->blocked()->create())
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_pemisahan_tugas_keuangan(): void
    {
        $manager = User::factory()->manager()->create();
        $cashier = User::factory()->cashier()->create();

        $this->assertTrue($cashier->can('catat-transaksi'));
        $this->assertFalse($cashier->can('koreksi-transaksi'));
        $this->assertTrue($manager->can('koreksi-transaksi'));
        $this->assertFalse($manager->can('catat-transaksi'));
    }
}