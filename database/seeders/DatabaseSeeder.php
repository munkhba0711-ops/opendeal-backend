<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. АДМИН ХЭРЭГЛЭГЧ ҮҮСГЭХ
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Ерөнхий Админ',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. ҮНДСЭН АНГИЛЛУУД ҮҮСГЭХ
        $categories = ['Гитар', 'Төгөлдөр хуур', 'Бөмбөр', 'Үлээвэр', 'Хийл'];
        foreach ($categories as $cat) {
            \Illuminate\Support\Facades\DB::table('categories')->insertOrIgnore([
                'name' => $cat,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->call([
            StoreSeeder::class,
        ]);
    }
}
