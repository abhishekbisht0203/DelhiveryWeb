<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('status_id')->nullable()->constrained('shipment_statuses')->nullOnDelete();
            $table->string('status_slug');
            $table->enum('event_type', ['status_change', 'location_update', 'assignment', 'exception', 'manual']);
            $table->text('description');
            $table->string('location')->nullable();
            $table->foreignId('hub_id')->nullable()->constrained('hubs')->nullOnDelete();

            $table->string('actor_type')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();

            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['shipment_id', 'created_at']);
            $table->index('status_slug');
            $table->index(['actor_type', 'actor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_events');
    }
};
