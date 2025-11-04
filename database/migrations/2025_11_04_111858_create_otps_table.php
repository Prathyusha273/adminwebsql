<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->index();
            $table->string('otp', 6);
            $table->timestamp('expires_at');
            $table->boolean('verified')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['phone', 'verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otps');
    }
};
