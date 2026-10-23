<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table): void {
            $table->string('booking_reference', 40)->nullable()->unique();
            $table->decimal('price_per_traveler_mad', 10, 2)->nullable();
            $table->decimal('estimated_total_mad', 12, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table): void {
            $table->dropUnique(['booking_reference']);
            $table->dropColumn([
                'booking_reference',
                'price_per_traveler_mad',
                'estimated_total_mad',
            ]);
        });
    }
};
