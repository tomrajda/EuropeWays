<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightHistoryTable extends Migration
{
    public function up()
    {
        Schema::create('flight_history_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Use unsignedBigInteger for foreign key references
            $table->json('api_url'); // Use json type for storing JSON data
            $table->json('amount'); // Use json type for storing JSON data
            $table->timestamps();

            // Add a foreign key constraint if you have a users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('flight_history_data');
    }
}
