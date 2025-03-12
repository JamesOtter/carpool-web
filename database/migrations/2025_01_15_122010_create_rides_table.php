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
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recurring_id')->nullable()->constrained('recurring_rides')->onDelete('cascade');
            $table->enum('ride_type', ['request', 'offer']);
            $table->string('departure_address');
            $table->string('departure_id');
            $table->string('destination_address');
            $table->string('destination_id');
            $table->date('departure_date');
            $table->time('departure_time');
            $table->integer('number_of_passenger');
            $table->decimal('distance', 10, 2);
            $table->integer('duration');
            $table->decimal('price', 10, 2);
            $table->string('description')->nullable();
            $table->enum('status', ['active', 'booked', 'expired'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
