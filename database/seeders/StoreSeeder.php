<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Гитар', 'Төгөлдөр хуур', 'Бөмбөр', 'Үлээвэр', 'Хийл'];
        $brands = ['Yamaha', 'Fender', 'Roland', 'Korg', 'Casio', 'Gibson', 'Shure'];

        // 1. ТЕСТ ХУДАЛДАН АВАГЧ (Сэтгэгдэл бичихэд ашиглана)
        $testBuyer = User::create([
            'name' => 'Худалдан авагч Бат',
            'email' => 'buyer@test.com',
            'password' => Hash::make('password123'),
            'role' => 'buyer',
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        // 2. ТЕСТ ХУДАЛДАГЧ ҮҮСГЭХ (Та яг энэ хаягаар нэвтэрч орж тестлэнэ)
        $testSeller = User::create([
            'name' => 'Тест Худалдагч (Миний Дэлгүүр)',
            'email' => 'seller@test.com',           // <--- НЭВТРЭХ ИМЭЙЛ
            'password' => Hash::make('password123'), // <--- НЭВТРЭХ НУУЦ ҮГ
            'role' => 'seller',
            'created_at' => Carbon::now()->subMonths(6)
        ]);

        // ТЕСТ ХУДАЛДАГЧИД ЗОРИУЛСАН БАРАА БОЛОН СЭТГЭГДЭЛ
        for ($j = 1; $j <= 5; $j++) {
            $cat = $categories[array_rand($categories)];
            Product::create([
                'user_id' => $testSeller->id,
                'status' => 'active',
                'title' => '[МИНИЙ БАРАА] ' . $cat . ' - ' . rand(100, 999),
                'category_name' => $cat,
                'description' => 'Энэ бол миний оруулсан тест бараа. Би үүнийг засах, устгах эрхтэй байх ёстой.',
                'price' => number_format(rand(10, 500) * 10000) . ' ₮',
                'condition' => 'Маш сайн',
                'conditionColor' => 'bg-primary/10 text-primary',
                'isUsed' => 'Хэрэглэсэн',
                'isVerified' => rand(0, 1),
                'img' => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&q=80&w=800',
                // === ШИНЭЭР НЭМЭГДСЭН: Нарийвчилсан үзүүлэлт ===
                'specs' => [
                    'Брэнд' => $brands[array_rand($brands)],
                    'Өнгө' => 'Хар / Хүрэн',
                    'Жин' => rand(2, 10) . ' кг',
                    'Үйлдвэрлэсэн он' => rand(2015, 2023) . ' он'
                ],
                'created_at' => Carbon::now()->subDays(rand(1, 10))
            ]);
        }

        for ($k = 1; $k <= 3; $k++) {
            $cat = $categories[array_rand($categories)];
            Product::create([
                'user_id' => $testSeller->id,
                'status' => 'sold',
                'title' => '[ЗАРАГДСАН] ' . $cat . ' - ' . rand(100, 999),
                'category_name' => $cat,
                'description' => 'Энэ бараа аль хэдийн зарагдсан.',
                'price' => number_format(rand(10, 500) * 10000) . ' ₮',
                'condition' => 'Хуучин',
                'conditionColor' => 'bg-orange-100 text-orange-700',
                'isUsed' => 'Хэрэглэсэн',
                'isVerified' => rand(0, 1),
                'img' => 'https://images.unsplash.com/photo-1550291652-6ea9114a47b1?auto=format&fit=crop&q=80&w=800',
                'specs' => [
                    'Брэнд' => $brands[array_rand($brands)],
                    'Төлөв байдал' => 'Сэв зураас багатай',
                    'Үйлдвэрлэсэн он' => rand(2010, 2018) . ' он'
                ],
                'created_at' => Carbon::now()->subDays(rand(15, 60))
            ]);
        }

        Review::create([
            'seller_id' => $testSeller->id,
            'buyer_id' => $testBuyer->id,
            'rating' => 5,
            'comment' => 'Маш сайн худалдагч, бараагаа хурдан явуулсан. Тест амжилттай!',
            'created_at' => Carbon::now()->subDays(2)
        ]);


        // 3. БУСАД 5 ХУДАЛДАГЧИЙГ ҮҮСГЭХ (Та эдгээрийн барааг зөвхөн үзэх эрхтэй байна)
        $sellers = [
            ['name' => 'Vintage Tone Shop', 'email' => 'vintage@test.com'],
            ['name' => 'Pro Audio MN', 'email' => 'proaudio@test.com'],
            ['name' => 'Guitar Center UB', 'email' => 'guitar@test.com'],
            ['name' => 'Classic Strings', 'email' => 'classic@test.com'],
            ['name' => 'Drummer Zone', 'email' => 'drummer@test.com'],
        ];

        foreach ($sellers as $sellerData) {
            $seller = User::create([
                'name' => $sellerData['name'],
                'email' => $sellerData['email'],
                'password' => Hash::make('password'),
                'role' => 'seller',
                'created_at' => Carbon::now()->subMonths(rand(1, 12))
            ]);

            // ТУС БҮРД НЬ 5-6 ИДЭВХТЭЙ БАРАА ҮҮСГЭХ
            $activeCount = rand(5, 6);
            for ($j = 1; $j <= $activeCount; $j++) {
                $cat = $categories[array_rand($categories)];
                $price = rand(10, 500) * 10000;
                Product::create([
                    'user_id' => $seller->id,
                    'status' => 'active',
                    'title' => $cat . ' - Загвар ' . rand(100, 999),
                    'category_name' => $cat,
                    'description' => 'Маш сайн хадгалсан цэвэрхэн хөгжим. Шууд тоглоход бэлэн.',
                    'price' => number_format($price) . ' ₮',
                    'condition' => 'Маш сайн',
                    'conditionColor' => 'bg-primary/10 text-primary',
                    'isUsed' => 'Хэрэглэсэн',
                    'isVerified' => rand(0, 1),
                    'img' => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&q=80&w=800',
                    'specs' => [
                        'Брэнд' => $brands[array_rand($brands)],
                        'Өнгө' => 'Модон өнгө',
                        'Үйлдвэрлэгдсэн' => 'Япон'
                    ],
                    'created_at' => Carbon::now()->subDays(rand(1, 10))
                ]);
            }

            // ТУС БҮРД НЬ 3-4 ЗАРАГДСАН БАРАА ҮҮСГЭХ
            $soldCount = rand(3, 4);
            for ($k = 1; $k <= $soldCount; $k++) {
                $cat = $categories[array_rand($categories)];
                $price = rand(10, 500) * 10000;
                Product::create([
                    'user_id' => $seller->id,
                    'status' => 'sold',
                    'title' => $cat . ' - Загвар ' . rand(100, 999),
                    'category_name' => $cat,
                    'description' => 'Энэ бараа зарагдсан.',
                    'price' => number_format($price) . ' ₮',
                    'condition' => 'Хуучин',
                    'conditionColor' => 'bg-orange-100 text-orange-700',
                    'isUsed' => 'Хэрэглэсэн',
                    'isVerified' => rand(0, 1),
                    'img' => 'https://images.unsplash.com/photo-1550291652-6ea9114a47b1?auto=format&fit=crop&q=80&w=800',
                    'specs' => [
                        'Брэнд' => $brands[array_rand($brands)],
                        'Ашигласан хугацаа' => rand(1, 5) . ' жил'
                    ],
                    'created_at' => Carbon::now()->subDays(rand(15, 60))
                ]);
            }

            // ТУС БҮРД НЬ 1 СЭТГЭГДЭЛ НЭМЭХ
            Review::create([
                'seller_id' => $seller->id,
                'buyer_id' => $testBuyer->id,
                'rating' => rand(4, 5),
                'comment' => 'Түргэн шуурхай үйлчилгээтэй. Санал болгож байна.',
                'created_at' => Carbon::now()->subDays(rand(1, 20))
            ]);
        }
    }
}
