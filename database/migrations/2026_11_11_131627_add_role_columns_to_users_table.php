<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // String biasa, bukan ENUM MySQL, supaya menambah role cukup lewat App\Enums\UserRole
            $table->string('role', 20)->default('staff')->after('password')->index();
            $table->string('account_status', 20)->default('active')->after('role');
            $table->unsignedTinyInteger('failed_login_attempts')->default(0)->after('account_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'account_status', 'failed_login_attempts']);
        });
    }
};