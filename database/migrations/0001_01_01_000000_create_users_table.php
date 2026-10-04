<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table): void {
                $table->uuid('id')->primary();
                $table->string('full_name')->nullable();
                $table->string('email')->unique();
                $table->text('avatar_url')->nullable();
                $table->timestampTz('created_at')->useCurrent();
            });

            if (DB::connection()->getDriverName() === 'pgsql'
                && DB::table('information_schema.tables')
                    ->where('table_schema', 'auth')
                    ->where('table_name', 'users')
                    ->exists()) {
                DB::statement('ALTER TABLE "public"."users" ADD CONSTRAINT "users_id_foreign" FOREIGN KEY ("id") REFERENCES "auth"."users" ("id") ON DELETE CASCADE');
            }
        }

        if (! Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->uuid('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Profile records belong to Supabase Auth; keep them intact on rollback.
    }
};
