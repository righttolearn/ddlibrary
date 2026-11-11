<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('glossary_page_views');
    }

    public function down(): void
    {
        Schema::create('glossary_page_views', function (Blueprint $table) {
            $table->id();
            $table->string('user_agent');
            $table->string('browser', 45);
            $table->string('title')->nullable();
            $table->boolean('is_bot')->default(false);
            $table->string('language', 4);
            $table->string('gender', 11)->nullable();
            $table->foreignId('device_id')->constrained();
            $table->foreignId('platform_id')->constrained();
            $table->foreignId('browser_id')->constrained();
            $table->unsignedTinyInteger('status');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('glossary_subject_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('glossary_subject_id')->references('id')->on('glossary_subjects');
            $table->index('gender');
        });
    }
};
