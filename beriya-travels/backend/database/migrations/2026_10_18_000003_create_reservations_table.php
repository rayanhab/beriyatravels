<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('offer_id')->constrained()->restrictOnDelete();
            $table->string('full_name', 120);
            $table->string('email', 160);
            $table->string('phone', 30)->nullable();
            $table->date('departure_date');
            $table->unsignedTinyInteger('travelers');
            $table->text('message')->nullable();
            $table->string('status', 24)->default('pending');
            $table->timestamps();
            $table->index(['status', 'departure_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
