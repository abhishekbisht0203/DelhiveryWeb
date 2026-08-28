<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pickup_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['successful', 'failed']);
            $table->timestamp('attempted_at');
            $table->string('failure_reason')->nullable();
            $table->text('remarks')->nullable();
            $table->string('proof_image')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_attempts');
    }
};
