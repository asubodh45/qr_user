<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_profile_id')->constrained('user_profiles')->cascadeOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('country')->default('Unknown');
            $table->string('city')->default('Unknown');
            $table->string('browser')->default('Unknown');
            $table->string('operating_system')->default('Unknown');
            $table->string('device')->default('Unknown');
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_scans');
    }
};
