<?php

namespace Tests\Feature;

use App\Models\ExpenseTransaction;
use App\Models\Order;
use App\Models\SalesTransaction;
use App\Models\TransactionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceTest extends TestCase
{
    use RefreshDatabase;

    private function order(int $total = 50000): Order
    {
        $order = Order::create([
            'order_number' => 'ORD-T-' . uniqid(),
            'customer_name' => 'Pelanggan',
            'guest_count' => 2,
            'table_number' => '3',
            'type' => 'offline',
            'status' => 'pending',
        ]);
        $order->total = $total;
        $order->save();

        return $order;
    }

    public function test_akses_halaman_keuangan_sesuai_role(): void
    {
        $this->actingAs(User::factory()->cashier()->create());
        $this->get(route('finance.sales.index'))->assertOk();
        $this->get(route('finance.expenses.index'))->assertOk();
        $this->get(route('finance.expenses.create'))->assertOk();
        $this->get(route('finance.report'))->assertForbidden();
        $this->get(route('finance.corrections.index'))->assertForbidden();

        $this->actingAs(User::factory()->manager()->create());
        $this->get(route('finance.report'))->assertOk();
        $this->get(route('finance.corrections.index'))->assertOk();
        $this->get(route('finance.sales.index'))->assertForbidden();

        $this->actingAs(User::factory()->staff()->create());
        $this->get(route('finance.expenses.index'))->assertForbidden();
    }

    public function test_kasir_mencatat_pengeluaran_dan_validasi(): void
    {
        $this->actingAs(User::factory()->cashier()->create());

        $this->post(route('finance.expenses.store'), [])
            ->assertSessionHasErrors(['category', 'amount', 'transaction_date']);

        $this->post(route('finance.expenses.store'), [
            'category' => 'Bahan baku',
            'amount' => 150000,
            'description' => 'Beli telur',
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('finance.expenses.index'));

        $this->assertDatabaseHas('expense_transactions', ['amount' => 150000, 'description' => 'Beli telur']);
    }

    public function test_kasir_mencatat_penjualan_sesuai_total_order(): void
    {
        $this->actingAs(User::factory()->cashier()->create());
        $order = $this->order(50000);

        $this->post(route('finance.sales.store'), ['order_id' => $order->id, 'payment_method' => 'QRIS', 'amount' => 1000])
            ->assertSessionHasErrors('amount', null, 'sales');

        $this->post(route('finance.sales.store'), ['order_id' => $order->id, 'payment_method' => 'QRIS', 'amount' => 50000])
            ->assertRedirect();
        $this->assertDatabaseHas('sales_transactions', ['order_id' => $order->id, 'amount' => 50000]);

        // Order yang sudah tercatat tidak bisa dicatat dua kali.
        $this->post(route('finance.sales.store'), ['order_id' => $order->id, 'payment_method' => 'Tunai', 'amount' => 50000])
            ->assertSessionHas('error');
        $this->assertSame(1, SalesTransaction::count());
    }

    public function test_manajer_mengoreksi_dan_tercatat_di_log(): void
    {
        $expense = ExpenseTransaction::create([
            'category' => 'Bahan baku', 'amount' => 150000, 'description' => 'Beli telur', 'transaction_date' => now()->toDateString(),
        ]);

        $this->actingAs(User::factory()->cashier()->create())
            ->put(route('finance.expenses.update', $expense), ['category' => 'Bahan baku', 'amount' => 1, 'transaction_date' => now()->toDateString()])
            ->assertForbidden();

        $this->actingAs(User::factory()->manager()->create());
        $data = ['category' => 'Bahan baku', 'amount' => 175000, 'description' => 'Beli telur', 'transaction_date' => now()->toDateString()];
        $this->put(route('finance.expenses.update', $expense), $data)->assertRedirect(route('finance.report'));
        $this->assertSame(175000, $expense->fresh()->amount);

        $this->delete(route('finance.expenses.destroy', $expense))->assertRedirect(route('finance.report'));
        $this->assertDatabaseMissing('expense_transactions', ['id' => $expense->id]);
        $this->assertSame(['ubah', 'hapus'], TransactionLog::orderBy('id')->pluck('action')->all());
    }
}
