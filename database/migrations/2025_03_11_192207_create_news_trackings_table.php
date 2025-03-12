<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('news_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->cascadeOnDelete();
            $table->string('device_id');         // Anonymized device identifier (hashed)
            $table->string('user_agent')->nullable();        // Browser/device info
            $table->string('ip_hash')->nullable();           // Hashed IP address for general location tracking without storing actual IP
            $table->integer('read_time_seconds')->default(0); // Time spent reading
            $table->string('referrer')->nullable();          // Where the reader came from
            $table->boolean('is_completed')->default(false);      // Whether they read to the end (or spent enough time reading)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('news_trackings');
    }
};
