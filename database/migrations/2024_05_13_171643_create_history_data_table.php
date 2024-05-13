<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryDataTable extends Migration
{
    public function up()
    {
        Schema::create('history_data', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('api_url');
            $table->string('amount');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('history_data');
    }
}
