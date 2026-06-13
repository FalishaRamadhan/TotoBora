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
        Schema::create('guardians', function (Blueprint $table) {
            $table->id('guardian_id');
            $table->foreignId('child_id')->constrained('children', 'child_id')->cascadeOnDelete();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('phone_number', 15)->notNull()->unique();
            $table->string('email', 100)->nullable();
            $table->string('relationship', 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
