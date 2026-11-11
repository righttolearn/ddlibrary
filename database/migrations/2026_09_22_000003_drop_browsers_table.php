<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('browsers');
    }

    public function down(): void
    {
        Schema::create('browsers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
        });
    }
};
