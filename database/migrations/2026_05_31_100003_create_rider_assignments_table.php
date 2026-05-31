<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->date('assigned_date');
            $table->date('unassigned_date')->nullable();
            $table->enum('status', ['active', 'completed', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->decimal('earnings_during_assignment', 12, 2)->default(0);
            $table->integer('deliveries_during_assignment')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_assignments');
    }
};
