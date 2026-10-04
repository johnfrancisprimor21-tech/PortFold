<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $templates = [
            ['id' => '2e3a107b-2305-4ba7-97ad-a69a79995201', 'name' => 'Simple', 'slug' => 'simple', 'category' => 'Clean & professional', 'style_tags' => 'Minimal, readable'],
            ['id' => '2e3a107b-2305-4ba7-97ad-a69a79995202', 'name' => 'Modern', 'slug' => 'modern', 'category' => 'Visual & dynamic', 'style_tags' => 'Editorial, interactive'],
            ['id' => '2e3a107b-2305-4ba7-97ad-a69a79995203', 'name' => 'Creative', 'slug' => 'creative', 'category' => 'Bold & unique', 'style_tags' => 'Expressive, immersive'],
        ];

        foreach ($templates as $template) {
            if (! DB::table('templates')->where('slug', $template['slug'])->exists()) {
                $attributes = $template;
                if (Schema::hasColumn('templates', 'created_at')) {
                    $attributes['created_at'] = now();
                }
                if (Schema::hasColumn('templates', 'updated_at')) {
                    $attributes['updated_at'] = now();
                }

                DB::table('templates')->insert($attributes);
            }
        }
    }
}
