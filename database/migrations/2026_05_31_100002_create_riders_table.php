<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->string('rider_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('photo')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('nin')->nullable();
            $table->string('bvn')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended', 'terminated'])->default('active');
            $table->decimal('compliance_score', 5, 2)->default(100);
            $table->decimal('performance_score', 5, 2)->default(100);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->integer('total_deliveries')->default(0);
            $table->integer('accident_count')->default(0);
            $table->integer('violation_count')->default(0);
            $table->boolean('background_check_passed')->default(false);
            $table->date('background_check_date')->nullable();
            $table->foreignId('current_motorcycle_id')->nullable()->constrained('motorcycles')->nullOnDelete();
            $table->date('assignment_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riders');
    }
};
