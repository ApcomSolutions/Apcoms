<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check if table already exists and drop it if needed
if (Schema::hasTable('insight_trackings')) {
    Schema::drop('insight_trackings');
    echo "Dropped existing insight_trackings table.\n";
}

// Create the new table
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

echo "Created insight_trackings table successfully.\n";
