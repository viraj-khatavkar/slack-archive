<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        DB::table('channels')->insert([
            ['name' => 'announcement'],
            ['name' => 'books'],
            ['name' => 'chart-analysis'],
            ['name' => 'financial_planning'],
            ['name' => 'fixed-income'],
            ['name' => 'fund-letters'],
            ['name' => 'futures-options'],
            ['name' => 'general'],
            ['name' => 'global-markets'],
            ['name' => 'health'],
            ['name' => 'maths'],
            ['name' => 'momentum'],
            ['name' => 'mutualfund'],
            ['name' => 'research_reports'],
            ['name' => 'systems_investing'],
            ['name' => 'tech'],
            ['name' => 'travel'],
            ['name' => 'value-investing'],
        ]);
    }
}
