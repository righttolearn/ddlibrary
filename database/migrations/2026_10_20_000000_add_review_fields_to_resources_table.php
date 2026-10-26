<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->timestamp('reviewed_at')->nullable()->index();
            $table->decimal('review_hours', 5, 2)->nullable();
            $table->unsignedInteger('reviewed_by')->nullable()->index();

            $table->foreign('reviewed_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropIndex(['reviewed_at']);
            $table->dropIndex(['reviewed_by']);
            $table->dropColumn(['reviewed_at', 'review_hours', 'reviewed_by']);
        });
    }
};
