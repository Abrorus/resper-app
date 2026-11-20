<?php

namespace App\Http\Requests\Akun;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    protected $errorBag = 'create';

    public function authorize(): bool
    {
        return $this->user()->can('kelola-pengguna');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9]{8,15}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'account_status' => ['required', Rule::enum(AccountStatus::class)],
        ];
    }

    public function messages(): array
    {
        return UserRequestMessages::MESSAGES;
    }
}
