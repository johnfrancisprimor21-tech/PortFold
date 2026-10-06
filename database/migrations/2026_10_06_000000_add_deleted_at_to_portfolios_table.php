<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('portfolios')) {
            throw new RuntimeException('The portfolios table is required before enabling recoverable deletion.');
        }

        if (! Schema::hasColumn('portfolios', 'deleted_at')) {
            Schema::table('portfolios', function (Blueprint $table): void {
                $table->timestampTz('deleted_at')->nullable();
            });
        }

        if (! Schema::hasIndex('portfolios', ['deleted_at'])) {
            Schema::table('portfolios', function (Blueprint $table): void {
                $table->index('deleted_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('portfolios') && Schema::hasColumn('portfolios', 'deleted_at')) {
            if (Schema::hasIndex('portfolios', ['deleted_at'])) {
                Schema::table('portfolios', function (Blueprint $table): void {
                    $table->dropIndex(['deleted_at']);
                });
            }

            Schema::table('portfolios', function (Blueprint $table): void {
                $table->dropColumn('deleted_at');
            });
        }
    }
};
