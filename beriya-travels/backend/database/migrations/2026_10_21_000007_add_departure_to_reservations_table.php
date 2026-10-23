<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table): void {
            $table->foreignId('departure_id')->nullable()->after('offer_id')->constrained()->restrictOnDelete();
            $table->index(['departure_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table): void {
            $table->dropIndex(['departure_id', 'status']);
            $table->dropConstrainedForeignId('departure_id');
        });
    }
};
