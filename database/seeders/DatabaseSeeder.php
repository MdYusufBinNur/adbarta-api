<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->create([
            'full_name' => 'Ad Barta',
            'email' => 'super@gmail.com',
            'uid' => Str::uuid(),
            'photo' => '',
            'phone' => '01815625375',
            'website' => 'https://binnur.xyz',
            'company' => 'AdBarta',
            'about' => 'This is about',
            'role' => 'super_admin',
            'password' => bcrypt('password'),
            'status' => 'approved',
        ]);
    }
}
