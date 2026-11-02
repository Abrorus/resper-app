<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class InventoryController extends Controller
{
    private const PER_PAGE = 5;

    /** Inventaris-1..3: Read Inventory (daftar + pencarian + paginasi). */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $inventories = Inventory::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('manager_name', 'like', "%{$search}%");
                });
            })
            ->orderBy('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('inventory', [
            'inventories' => $inventories,
            'totalInventory' => Inventory::count(),
            'search' => $search,
        ]);
    }

    /** Inventaris-2: ambil data stok Inventory Item saat ini dari database untuk pop-up Update. */
    public function show(Inventory $inventory): JsonResponse
    {
        return response()->json([
            'name' => $inventory->name,
            'code' => $inventory->code,
            'total' => $inventory->total,
            'date' => $inventory->date->toDateString(),
            'manager_name' => $inventory->manager_name,
            'position' => $inventory->position,
        ]);
    }

    /** Inventaris-1: Add Inventory. */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('create', $this->rules());

        try {
            Inventory::create($data);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Penyimpanan data inventaris gagal.')->with('modal', 'create');
        }

        return redirect()->route('inventory.index')->with('success', 'Inventaris berhasil ditambahkan.');
    }

    /** Inventaris-2: Update Inventory. */
    public function update(Request $request, Inventory $inventory): RedirectResponse
    {
        $data = $request->validateWithBag('update', $this->rules($inventory));

        try {
            $inventory->update($data);
        } catch (Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Pembaruan data inventaris gagal.')
                ->with('modal', 'update')->with('modal_id', $inventory->id);
        }

        return redirect()->route('inventory.index', $request->only('page', 'search'))
            ->with('success', 'Inventaris berhasil diperbarui.');
    }

    /** Inventaris-3: Delete Inventory. */
    public function destroy(Request $request, Inventory $inventory): RedirectResponse
    {
        try {
            $inventory->delete();
        } catch (Throwable $e) {
            report($e);

            return redirect()->route('inventory.index')->with('error', 'Penghapusan data inventaris gagal.');
        }

        return redirect()->route('inventory.index', $request->only('page', 'search'))
            ->with('success', 'Inventaris berhasil dihapus.');
    }

    private function rules(?Inventory $inventory = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', Rule::unique('inventories', 'code')->ignore($inventory?->id)],
            'total' => ['required', 'integer', 'min:0'],
            'date' => ['required', 'date'],
            'manager_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
        ];
    }
}

