<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ndr_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_partner_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hub_id')->nullable()->constrained('hubs')->nullOnDelete();

            $table->integer('attempt_number');
            $table->string('reason');
            $table->text('remarks');
            $table->text('customer_response')->nullable();

            $table->string('next_action')->nullable();
            $table->date('reattempt_date')->nullable();

            $table->enum('status', ['open', 'reattempted', 'rto_initiated', 'resolved', 'cancelled'])->default('open');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('shipment_id');
            $table->index('status');
            $table->index('reattempt_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ndr_records');
    }
};
