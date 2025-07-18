<?php

namespace Database\Seeders;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userIds = User::pluck('id')->toArray();

        Blog::factory()->count(10)->create([
            'user_id' =>fn() =>fake()->randomElement($userIds),
        ]);
    }
}
