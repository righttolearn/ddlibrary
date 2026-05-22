<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('static_subject_area_icons', function (Blueprint $table) {
            $table->string('phosphor_icon', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('static_subject_area_icons', function (Blueprint $table) {
            $table->dropColumn('phosphor_icon');
        });
    }
};
