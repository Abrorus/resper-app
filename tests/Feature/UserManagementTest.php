<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->manager()->create();
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'name' => 'Kasir Baru',
            'email' => 'kasir.baru@resper.test',
            'phone' => '081200000001',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'role' => 'cashier',
            'account_status' => 'active',
        ], $override);
    }

    public function test_hanya_manajer_yang_bisa_membuka_kelola_pengguna(): void
    {
        $this->actingAs($this->manager)->get(route('akun.users.index'))->assertOk()->assertSee('Kelola Pengguna');
        $this->actingAs(User::factory()->cashier()->create())->get(route('akun.users.index'))->assertForbidden();
        $this->actingAs(User::factory()->staff()->create())->get(route('akun.users.index'))->assertForbidden();
    }

    public function test_index_mendukung_pencarian_dan_filter_role(): void
    {
        User::factory()->cashier()->create(['name' => 'Budi Kasir', 'email' => 'budi@resper.test']);
        User::factory()->staff()->create(['name' => 'Sari Staff', 'email' => 'sari@resper.test']);

        $this->actingAs($this->manager)
            ->get(route('akun.users.index', ['search' => 'budi']))
            ->assertOk()->assertSee('Budi Kasir')->assertDontSee('Sari Staff');

        $this->actingAs($this->manager)
            ->get(route('akun.users.index', ['role' => 'staff']))
            ->assertOk()->assertSee('Sari Staff')->assertDontSee('Budi Kasir');
    }

    public function test_manajer_bisa_menambah_pengguna(): void
    {
        $this->actingAs($this->manager)
            ->post(route('akun.users.store'), $this->payload())
            ->assertRedirect(route('akun.users.index'))
            ->assertSessionHas('success');

        $user = User::where('email', 'kasir.baru@resper.test')->first();
        $this->assertSame(UserRole::Cashier, $user->role);
        $this->assertSame('081200000001', $user->phone);
        $this->assertTrue(Hash::check('rahasia123', $user->password));
    }

    public function test_tambah_pengguna_memvalidasi_input_dengan_error_bag_create(): void
    {
        $this->actingAs($this->manager)
            ->post(route('akun.users.store'), $this->payload(['email' => $this->manager->email, 'role' => 'admin', 'password' => '123', 'password_confirmation' => '123']))
            ->assertSessionHasErrors(['email', 'role', 'password'], errorBag: 'create');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_kasir_tidak_bisa_menambah_pengguna(): void
    {
        $this->actingAs(User::factory()->cashier()->create())
            ->post(route('akun.users.store'), $this->payload())
            ->assertForbidden();
    }

    public function test_manajer_bisa_mengubah_pengguna_tanpa_mengganti_password(): void
    {
        $user = User::factory()->staff()->create(['password' => 'lama12345']);

        $this->actingAs($this->manager)
            ->put(route('akun.users.update', $user), $this->payload([
                'name' => 'Nama Baru',
                'email' => 'baru@resper.test',
                'password' => '',
                'password_confirmation' => '',
                'role' => 'cashier',
            ]))
            ->assertRedirect(route('akun.users.index'))
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame(UserRole::Cashier, $user->role);
        $this->assertTrue(Hash::check('lama12345', $user->password));
    }

    public function test_mengubah_password_pengguna(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($this->manager)
            ->put(route('akun.users.update', $user), $this->payload(['email' => $user->email, 'password' => 'barubanget1', 'password_confirmation' => 'barubanget1', 'role' => 'staff']));

        $this->assertTrue(Hash::check('barubanget1', $user->fresh()->password));
    }

    public function test_memulihkan_akun_terblokir_mereset_hitungan_gagal(): void
    {
        $blocked = User::factory()->blocked()->create(['failed_login_attempts' => 5]);

        $this->actingAs($this->manager)
            ->put(route('akun.users.update', $blocked), $this->payload(['email' => $blocked->email, 'password' => '', 'password_confirmation' => '', 'role' => 'staff', 'account_status' => 'active']));

        $this->assertSame(AccountStatus::Active, $blocked->fresh()->account_status);
        $this->assertSame(0, $blocked->fresh()->failed_login_attempts);
    }

    public function test_manajer_tidak_bisa_mengubah_role_sendiri(): void
    {
        $this->actingAs($this->manager)
            ->put(route('akun.users.update', $this->manager), $this->payload(['email' => $this->manager->email, 'password' => '', 'password_confirmation' => '', 'role' => 'staff']))
            ->assertSessionHasErrors('role', errorBag: 'update');

        $this->assertSame(UserRole::Manager, $this->manager->fresh()->role);
    }

    public function test_validasi_gagal_membuka_kembali_modal_ubah_dengan_data_lama(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($this->manager)
            ->from(route('akun.users.index'))
            ->followingRedirects()
            ->put(route('akun.users.update', $user), $this->payload(['user_id' => $user->id, 'name' => 'Nama Diubah', 'email' => 'bukan-email', 'role' => 'staff']))
            ->assertOk()
            ->assertSee("open('update')", false)
            ->assertSee(route('akun.users.update', $user), false)
            ->assertSee('value="Nama Diubah"', false)
            ->assertSee('Format email tidak valid.');
    }

    public function test_manajer_bisa_menghapus_pengguna_lain(): void
    {
        $user = User::factory()->staff()->create();

        $this->actingAs($this->manager)->delete(route('akun.users.destroy', $user))->assertRedirect(route('akun.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_manajer_tidak_bisa_menghapus_diri_sendiri(): void
    {
        $this->actingAs($this->manager)->delete(route('akun.users.destroy', $this->manager))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->manager->id]);
    }
}
