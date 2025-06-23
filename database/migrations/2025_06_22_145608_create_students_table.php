<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->decimal('pickup_lat', 10, 6)->nullable();
            $table->decimal('pickup_lng', 10, 6)->nullable();
            $table->decimal('dropoff_lat', 10, 6)->nullable();
            $table->decimal('dropoff_lng', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
