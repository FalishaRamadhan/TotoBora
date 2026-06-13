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
        Schema::create('growth_measurements', function (Blueprint $table) {
            $table->id('measurement_id');
            $table->foreignId('child_id')->constrained('children', 'child_id');
            $table->date('date_measured')->notNull();
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('height_cm', 5, 2);
            $table->enum('who_weight_status', ['Normal', 'At Risk', 'Underweight'])->default('Normal');
            $table->enum('who_height_status', ['Normal', 'At Risk', 'Stunted'])->default('Normal');
            $table->foreignId('worker_id')->constrained('users', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('growth_measurements');
    }
};
