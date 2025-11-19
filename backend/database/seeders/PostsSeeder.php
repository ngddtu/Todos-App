<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            DB::table('posts')->insert([
                'title' => fake()->text(25),
                'description' => fake()->text(50),
                'content' => fake()->paragraph(),
                'image' => null
            ]);
        }
    }
}
