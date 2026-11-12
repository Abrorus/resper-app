<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // READ: halaman Pesanan (daftar menu + riwayat pesanan offline)
    public function pesanan()
    {
        $orders = Order::with('items')
            ->where('type', 'offline')
            ->latest()
            ->take(10)
            ->get();

        return view('order.pesanan', [
            'menus'  => Menu::all(),
            'orders' => $orders,
        ]);
    }

    // READ: halaman Delivery Order (pesanan masuk dari pihak ketiga)
    public function delivery()
    {
        $orders = Order::with('items')
            ->where('type', 'third_party')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('order.delivery', compact('orders'));
    }

    // CREATE: simpan pesanan offline baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name'    => 'nullable|string|max:100',
            'guest_count'      => 'nullable|integer|min:1',
            'table_number'     => 'nullable|string|max:10',
            'items'            => 'required|array|min:1',
            'items.*.menu_id'  => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($data) {
            $order = Order::create([
                'order_number'  => 'ORD-' . now()->format('ymdHis'),
                'customer_name' => $data['customer_name'] ?? null,
                'guest_count'   => $data['guest_count'] ?? null,
                'table_number'  => $data['table_number'] ?? null,
            ]);

            $total = 0;
            foreach ($data['items'] as $row) {
                $menu = Menu::find($row['menu_id']);
                $order->items()->create([
                    'menu_name' => $menu->name,
                    'price'     => $menu->price,
                    'quantity'  => $row['quantity'],
                ]);
                $total += $menu->price * $row['quantity'];
            }

            $order->update(['total' => $total]);
        });

        return redirect()->route('order.pesanan')->with('success', 'Pesanan berhasil dibuat.');
    }

    // UPDATE: proses pesanan (ubah status menjadi processed)
    public function process(Order $order)
    {
        $order->update(['status' => 'processed']);

        return back()->with('success', 'Pesanan diproses.');
    }

    // DELETE: batalkan pesanan (ubah status menjadi cancelled)
    public function destroy(Order $order)
    {
        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Pesanan dibatalkan.');
    }

    // Simulasi pesanan masuk dari platform pihak ketiga (untuk demo)
    public function simulate()
    {
        $menu = Menu::inRandomOrder()->first();

        if (! $menu) {
            return back()->with('success', 'Belum ada data menu. Jalankan MenuSeeder dulu.');
        }

        $qty = rand(1, 3);

        $order = Order::create([
            'order_number'  => 'ORD-' . now()->format('ymdHis'),
            'customer_name' => 'Pelanggan Online',
            'type'          => 'third_party',
            'platform'      => collect(['GoFood', 'GrabFood', 'ShopeeFood'])->random(),
            'total'         => $menu->price * $qty,
        ]);

        $order->items()->create([
            'menu_name' => $menu->name,
            'price'     => $menu->price,
            'quantity'  => $qty,
        ]);

        return back()->with('success', 'Pesanan masuk disimulasikan.');
    }
}
