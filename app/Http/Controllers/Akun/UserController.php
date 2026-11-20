<?php

namespace App\Http\Controllers\Akun;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Akun\StoreUserRequest;
use App\Http\Requests\Akun\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD data pengguna oleh Manajer (modul Akun).
 */
class UserController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $request->validate(['role' => ['nullable', Rule::enum(UserRole::class)]]);

        $search = trim((string) $request->query('search', ''));
        $filter = $request->enum('role', UserRole::class);

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filter, fn ($query) => $query->role($filter))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('akun.users', [
            'users' => $users,
            'totalUsers' => User::count(),
            'roles' => UserRole::cases(),
            'statuses' => AccountStatus::cases(),
            'search' => $search,
            'filter' => $filter,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = new User($data);
        $user->role = UserRole::from($data['role']);
        $user->account_status = AccountStatus::from($data['account_status']);
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('akun.users.index')
            ->with('success', "Pengguna {$user->name} berhasil ditambahkan.");
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $newRole = UserRole::from($data['role']);
        $newStatus = AccountStatus::from($data['account_status']);

        // Manajer tidak boleh menurunkan role atau memblokir akunnya sendiri (mencegah terkunci dari sistem)
        if ($user->is($request->user()) && ($newRole !== $user->role || $newStatus !== $user->account_status)) {
            return back()->withInput()
                ->withErrors(['role' => 'Anda tidak dapat mengubah role atau status akun sendiri.'], 'update');
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        // Memulihkan akun terblokir sekaligus mereset hitungan gagal login
        if ($user->account_status === AccountStatus::Blocked && $newStatus === AccountStatus::Active) {
            $user->failed_login_attempts = 0;
        }

        $user->role = $newRole;
        $user->account_status = $newStatus;
        $user->save();

        return redirect()->route('akun.users.index', $request->only('page', 'search', 'role_filter'))
            ->with('success', "Pengguna {$user->name} berhasil diperbarui.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('akun.users.index', $request->only('page', 'search'))
            ->with('success', "Pengguna {$user->name} berhasil dihapus.");
    }
}
