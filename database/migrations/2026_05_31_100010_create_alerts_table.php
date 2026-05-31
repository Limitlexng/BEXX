<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['insurance_expiry', 'license_expiry', 'maintenance_due', 'inactive_rider', 'low_utilization', 'asset_suspension', 'withdrawal_approval', 'earnings_upload', 'compliance_violation', 'general'])->default('general');
            $table->string('title');
            $table->text('message');
            $table->enum('severity', ['info', 'warning', 'danger', 'success'])->default('info');
            $table->morphs('alertable');
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
