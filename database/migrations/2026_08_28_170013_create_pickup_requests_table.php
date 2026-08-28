<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pickup_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('merchant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hub_id')->nullable()->constrained('hubs')->nullOnDelete();

            $table->text('pickup_address')->nullable();
            $table->string('pickup_city')->nullable();
            $table->string('pickup_state')->nullable();
            $table->string('pickup_pincode', 10)->nullable();
            $table->string('pickup_phone', 20)->nullable();
            $table->string('pickup_contact_name')->nullable();

            $table->date('requested_date');
            $table->string('requested_time_slot')->nullable();

            $table->foreignId('assigned_to')->nullable()->constrained('delivery_partners')->nullOnDelete();

            $table->enum('status', ['pending', 'assigned', 'scheduled', 'picked_up', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();

            $table->integer('attempt_count')->default(0);
            $table->integer('max_attempts')->default(3);

            $table->string('failure_reason')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('requested_date');
            $table->index('merchant_id');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pickup_requests');
    }
};
