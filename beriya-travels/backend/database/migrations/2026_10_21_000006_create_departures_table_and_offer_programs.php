<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table): void {
            $table->json('program')->nullable();
        });

        Schema::create('departures', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->string('status', 20)->default('open');
            $table->string('note', 255)->nullable();
            $table->timestamps();
            $table->unique(['offer_id', 'departure_date']);
            $table->index(['status', 'departure_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departures');

        Schema::table('offers', function (Blueprint $table): void {
            $table->dropColumn('program');
        });
    }
};
