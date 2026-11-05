<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('sitewide_page_views');
    }

    public function down(): void
    {
        Schema::create('sitewide_page_views', function (Blueprint $table) {
            $table->id();
            $table->string('page_url');
            $table->text('user_agent')->nullable();
            $table->string('browser', 45);
            $table->string('title')->nullable();
            $table->boolean('is_bot')->default(false);
            $table->string('language', 4);
            $table->string('gender', 11)->nullable();
            $table->foreignId('device_id')->constrained();
            $table->foreignId('platform_id')->constrained();
            $table->foreignId('browser_id')->constrained();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->timestamps();

            $table->index('gender');
            $table->index('is_bot');
            $table->index('created_at');
            $table->index(['is_bot', 'browser_id', 'created_at']);
            $table->index(['is_bot', 'platform_id', 'created_at']);
            $table->index(['is_bot', 'gender', 'created_at']);
            $table->index(['is_bot', 'created_at']);
            $table->index(['is_bot', 'created_at', 'user_id']);
            $table->index(['is_bot', 'page_url', 'title']);
            $table->index(['is_bot', 'created_at', 'page_url', 'title']);
        });
    }
};
