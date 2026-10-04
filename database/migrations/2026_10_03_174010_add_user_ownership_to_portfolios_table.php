<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('portfolios')) {
            throw new RuntimeException('The existing portfolios table is required before enabling account ownership.');
        }

        if (Schema::hasColumn('portfolios', 'user_id')) {
            if (! Schema::hasIndex('portfolios', ['user_id'])) {
                Schema::table('portfolios', function (Blueprint $table): void {
                    $table->index('user_id');
                });
            }

            return;
        }

        Schema::table('portfolios', function (Blueprint $table): void {
            $table->uuid('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Keep the nullable owner column and its data when rolling back application code.
    }
};
