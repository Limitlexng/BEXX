<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorcycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->string('fleet_id')->unique();
            $table->string('vehicle_number')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('vin_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->string('color')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->enum('insurance_status', ['active', 'expired', 'pending'])->default('pending');
            $table->date('insurance_expiry')->nullable();
            $table->date('road_worthiness_expiry')->nullable();
            $table->enum('status', ['active', 'maintenance', 'suspended', 'retired', 'lost'])->default('active');
            $table->enum('health_rating', ['excellent', 'good', 'average', 'poor', 'critical'])->default('good');
            $table->decimal('health_score', 5, 2)->default(100);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->decimal('total_maintenance_cost', 12, 2)->default(0);
            $table->string('current_location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->json('photos')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorcycles');
    }
};
