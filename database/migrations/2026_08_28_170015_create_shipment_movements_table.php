<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_hub_id')->nullable()->constrained('hubs')->nullOnDelete();
            $table->foreignId('to_hub_id')->nullable()->constrained('hubs')->nullOnDelete();
            $table->enum('movement_type', ['inbound', 'outbound', 'transfer', 'return']);
            $table->enum('status', ['in_transit', 'arrived', 'dispatched', 'held']);
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->string('vehicle_number', 20)->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone', 20)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('shipment_id');
            $table->index('from_hub_id');
            $table->index('to_hub_id');
            $table->index('dispatched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_movements');
    }
};
