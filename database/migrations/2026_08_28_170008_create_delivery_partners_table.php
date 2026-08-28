<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hub_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable();
            $table->enum('vehicle_type', ['bike', 'van', 'truck', 'cycle']);
            $table->string('vehicle_number', 20)->nullable();
            $table->string('license_number', 30)->nullable();
            $table->string('aadhar_number', 20)->nullable();
            $table->json('assigned_areas')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave', 'suspended'])->default('active');
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_partners');
    }
};
