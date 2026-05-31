<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['routine_service', 'repair', 'inspection', 'emergency', 'upgrade'])->default('routine_service');
            $table->string('description');
            $table->text('details')->nullable();
            $table->string('workshop_name')->nullable();
            $table->string('technician_name')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->date('service_date');
            $table->date('next_service_due')->nullable();
            $table->integer('downtime_hours')->default(0);
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('completed');
            $table->json('parts_replaced')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
