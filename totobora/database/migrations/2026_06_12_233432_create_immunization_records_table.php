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
        Schema::create('immunization_records', function (Blueprint $table) {
            $table->id('record_id');
            $table->foreignId('child_id')->constrained('children', 'child_id');
            $table->string('vaccine_name', 100)->notNull();
            $table->integer('dose_number')->nullable();
            $table->date('date_administered')->notNull();
            $table->date('next_due_date')->nullable();
            $table->foreignId('worker_id')->constrained('users', 'id');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunization_records');
    }
};
