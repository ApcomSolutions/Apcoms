<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInsightTrackingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('insight_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insight_id')->constrained()->onDelete('cascade');
            $table->string('device_id', 64)->nullable(); // Hashed unique device identifier
            $table->text('user_agent')->nullable();
            $table->string('ip_hash', 64)->nullable(); // Hashed IP for privacy
            $table->integer('read_time_seconds')->default(0);
            $table->string('referrer')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            // Index for efficient queries
            $table->index(['insight_id', 'device_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('insight_trackings');
    }
}
