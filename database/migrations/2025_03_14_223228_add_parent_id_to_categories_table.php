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
        Schema::table('categories', function (Blueprint $table) {
            // Add parent_id as a foreign key referencing the id column in the same table
            $table->foreignId('parent_id')->nullable()->after('id')
                ->references('id')->on('categories')
                ->onDelete('set null');

            // Add is_active boolean field with default true
            $table->boolean('is_active')->default(true)->after('description');

            // Add order integer field with default 0
            $table->integer('order')->default(0)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['parent_id']);

            // Drop the columns
            $table->dropColumn(['parent_id', 'is_active', 'order']);
        });
    }
};
