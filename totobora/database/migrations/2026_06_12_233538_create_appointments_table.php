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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->foreignId('child_id')->constrained('children', 'child_id');
            $table->date('scheduled_date')->notNull();
            $table->string('vaccine_due', 100)->nullable();
            $table->enum('status', ['scheduled', 'attended', 'missed'])->default('scheduled');
            $table->foreignId('worker_id')->constrained('users', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
