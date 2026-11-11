<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'role' => ['nullable', Rule::enum(UserRole::class)],
        ]);

        $filter = $request->enum('role', UserRole::class);

        $users = User::query()
            ->when($filter, fn ($query) => $query->role($filter))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('akun.role.index', [
            'users' => $users,
            'roles' => UserRole::cases(),
            'statuses' => AccountStatus::cases(),
            'filter' => $filter,
        ]);
    }

    public function update(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        // Manajer tidak bisa menurunkan atau memblokir dirinya sendiri,
        // sehingga selalu tersisa minimal satu manajer aktif
        if ($user->is($request->user())) {
            return back()->withErrors([
                'role' => 'Anda tidak dapat mengubah role atau status akun sendiri.',
            ]);
        }

        $newRole = $request->enum('role', UserRole::class);
        $newStatus = $request->enum('account_status', AccountStatus::class);

        // Memulihkan akun terblokir sekaligus mereset hitungan gagal login
        if ($user->account_status === AccountStatus::Blocked && $newStatus === AccountStatus::Active) {
            $user->failed_login_attempts = 0;
        }

        $user->role = $newRole;
        $user->account_status = $newStatus;
        $user->save();

        return back()->with(
            'success',
            "Akun {$user->name} sekarang {$newRole->label()} ({$newStatus->label()})."
        );
    }
}