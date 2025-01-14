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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->integer('rider_id');
            $table->string('departure_address');
            $table->float('departure_latitude');
            $table->float('departure_longitude');
            $table->string('destination_address');
            $table->float('destination_latitude');
            $table->float('destination_longitude');
            $table->integer('number_of_passengers');
            $table->float('price');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
