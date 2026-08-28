<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('phone', 20)->nullable()->after('password');
            $table->string('role_label')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('role_label');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('avatar');
            $table->timestamp('last_login_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn([
                'organization_id', 'phone', 'role_label', 'avatar', 'status', 'last_login_at',
            ]);
        });
    }
};
