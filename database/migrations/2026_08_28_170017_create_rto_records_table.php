<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rto_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ndr_record_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reason');
            $table->unsignedBigInteger('initiated_by')->nullable();
            $table->string('rto_awb', 20)->nullable();
            $table->enum('status', ['initiated', 'pickup_scheduled', 'picked_up', 'in_transit', 'at_hub', 'delivered']);
            $table->timestamp('initiated_at');
            $table->timestamp('completed_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rto_records');
    }
};
