<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('author')->nullable();
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->string('type')->default('website')->nullable();
            $table->json('schema')->nullable();
            $table->json('opengraph')->nullable();
            $table->json('twitter')->nullable();
            $table->json('meta')->nullable();
            $table->string('robots')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('seo');
    }
};
