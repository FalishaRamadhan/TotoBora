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
        Schema::create('reminders', function (Blueprint $table) {
            $table->id('reminder_id');
            $table->foreignId('appointment__id')->constrained('appointments', 'appointment_id');
            $table->foreignId('guardian_id')->constrained('guardians', 'guardian_id');
            $table->dateTime('send_datetime');
            $table->enum('channel', ['SMS', 'email'])->default('SMS');
            $table->enum('delivery_status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminders');
    }
};
