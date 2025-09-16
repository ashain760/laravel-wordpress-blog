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
        Schema::create('wp_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wp_id')->nullable()->index();
            $table->string('title');
            $table->text('content');
            $table->string('status')->default('draft');
            $table->integer('priority')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wp_posts');
    }
};
