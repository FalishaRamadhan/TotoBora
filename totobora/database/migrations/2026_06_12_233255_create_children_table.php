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
        Schema::create('children', function (Blueprint $table) {
            $table->id('child_id');
            $table->string('first_name', 50)->notNull();
            $table->string('last_name', 50)->notNull();
            $table->date('date_of_birth')->notNull();
            $table->enum('gender', ['Male', 'Female'])->notNull();
            $table->decimal('birth_weight', 4, 2)->nullable();
            $table->foreignId('facility_id')->constrained('facilities', 'facility_id');
            $table->string('unique_child')->unique(); //first_name+last_name+dob+facility_id composite handledin model
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
