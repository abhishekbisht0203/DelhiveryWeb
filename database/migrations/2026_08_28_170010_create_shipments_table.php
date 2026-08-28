<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('awb_number', 20)->unique();
            $table->string('order_id');
            $table->string('reference_number')->nullable();

            $table->foreignId('merchant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();

            $table->string('consignor_name');
            $table->string('consignor_phone', 20);
            $table->string('consignor_email')->nullable();
            $table->text('consignor_address')->nullable();
            $table->string('consignor_city')->nullable();
            $table->string('consignor_state')->nullable();
            $table->string('consignor_pincode', 10)->nullable();

            $table->string('consignee_name');
            $table->string('consignee_phone', 20);
            $table->string('consignee_email')->nullable();
            $table->text('consignee_address')->nullable();
            $table->string('consignee_city')->nullable();
            $table->string('consignee_state')->nullable();
            $table->string('consignee_pincode', 10)->nullable();

            $table->foreignId('origin_hub_id')->nullable()->constrained('hubs')->nullOnDelete();
            $table->foreignId('destination_hub_id')->nullable()->constrained('hubs')->nullOnDelete();
            $table->foreignId('current_hub_id')->nullable()->constrained('hubs')->nullOnDelete();
            $table->foreignId('delivery_partner_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('status_id')->nullable()->constrained('shipment_statuses')->nullOnDelete();
            $table->string('status_slug');

            $table->enum('payment_type', ['prepaid', 'cod', 'reverse', 'cod_reversal', 'to_pay'])->default('prepaid');
            $table->decimal('cod_amount', 10, 2)->default(0);
            $table->decimal('collected_amount', 10, 2)->default(0);
            $table->enum('cod_status', ['pending', 'collected', 'remitted', 'failed'])->default('pending');

            $table->decimal('invoice_amount', 10, 2)->default(0);
            $table->decimal('freight_charges', 10, 2)->default(0);
            $table->decimal('other_charges', 10, 2)->default(0);

            $table->text('package_description')->nullable();
            $table->integer('quantity')->default(1);

            $table->decimal('weight', 8, 2);
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->decimal('volumetric_weight', 8, 2)->nullable();

            $table->enum('service_type', ['surface', 'express', 'same_day', 'next_day', 'standard'])->default('standard');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');

            $table->date('expected_delivery_date')->nullable();
            $table->timestamp('actual_delivery_date')->nullable();

            $table->enum('pickup_status', ['pending', 'scheduled', 'assigned', 'picked_up', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('pickup_scheduled_at')->nullable();
            $table->timestamp('pickup_completed_at')->nullable();

            $table->string('current_location')->nullable();
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();

            $table->text('remarks')->nullable();
            $table->text('internal_notes')->nullable();

            $table->boolean('is_rto')->default(false);
            $table->string('rto_reason')->nullable();
            $table->boolean('is_returned')->default(false);

            $table->integer('ndr_count')->default(0);
            $table->integer('attempt_count')->default(0);

            $table->json('metadata')->nullable();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('awb_number');
            $table->index('order_id');
            $table->index('status_slug');
            $table->index('merchant_id');
            $table->index('organization_id');
            $table->index('consignee_pincode');
            $table->index('consignor_pincode');
            $table->index('payment_type');
            $table->index('cod_status');
            $table->index('service_type');
            $table->index('created_at');
            $table->index(['status_slug', 'created_at']);
            $table->index(['merchant_id', 'status_slug']);
            $table->index(['consignee_pincode', 'status_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
