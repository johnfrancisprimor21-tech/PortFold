<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supabase Auth owns provider identities; no provider-specific column is needed.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep identity data in Supabase Auth.
    }
};
