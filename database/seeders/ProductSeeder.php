<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ГИТАРУУД
            [
                'title' => 'Fender Stratocaster American Pro II',
                'category_name' => 'Гитар',
                'description' => '2021 онд АНУ-аас шинээр нь захиалж авчруулсан. Зөвхөн студид бичлэгт хэдхэн удаа ашигласан тул ямар ч зураас, сэв байхгүй цэмбэгэр. Мөнгөний хэрэг гарсан тул яаралтай хямд зарна. Оригинал хатуу кэйс дагалдана.',
                'price' => '3,450,000 ₮',
                'condition' => 'Маш сайн',
                'conditionColor' => 'bg-primary/10 text-primary',
                'isUsed' => 'Хэрэглэсэн',
                'isVerified' => true,
                'img' => 'https://images.unsplash.com/photo-1550291652-6ea9114a47b1?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1550291652-6ea9114a47b1?w=1000',
                    'https://images.unsplash.com/photo-1514649923863-ceaf75b770ab?w=1000',
                    'https://images.unsplash.com/photo-1525201548942-d8732f6617a0?w=1000',
                    'https://images.unsplash.com/photo-1485030056468-3820ff9e6e90?w=1000'
                ],
                'weight' => 3.5,
                'size_category' => 'medium',
                'specs' => [
                    'Брэнд' => 'Fender',
                    'Загвар' => 'American Professional II',
                    'Үйлдвэрлэсэн он' => '2021',
                    'Их биеийн мод' => 'Alder (Егүүд)',
                    'Хүзүүний мод' => 'Maple (Агч)',
                    'Өнгө' => '3-Color Sunburst',
                    'Хайрцаг' => 'Оригинал хатуу кэйс'
                ]
            ],
            [
                'title' => 'Gibson Les Paul Standard 60s',
                'category_name' => 'Гитар',
                // ЭНЭ БАРААНД ТАЙЛБАР ОРУУЛААГҮЙ ТУЛ ДЭЛГЭЦЭД ЮУ Ч ГАРАХГҮЙ
                'description' => null,
                'price' => '4,200,000 ₮',
                'condition' => 'Шинэ',
                'conditionColor' => 'bg-blue-100 text-blue-700',
                'isUsed' => 'Битүүмжилсэн',
                'isVerified' => true,
                'img' => 'https://images.unsplash.com/photo-1564186763535-ebb21ef5277f?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1564186763535-ebb21ef5277f?w=1000',
                    'https://images.unsplash.com/photo-1550985543-f47f38aeea53?w=1000',
                    'https://images.unsplash.com/photo-1464375117522-131205112050?w=1000'
                ],
                'weight' => 4.3,
                'size_category' => 'medium',
                'specs' => [
                    'Брэнд' => 'Gibson',
                    'Загвар' => 'Les Paul Standard 60s',
                    'Үйлдвэрлэсэн он' => '2023',
                    'Их биеийн мод' => 'Mahogany (Зандан)',
                    'Өнгө' => 'Iced Tea Burst',
                    'Пикап (Pickups)' => '60s Burstbucker'
                ]
            ],
            [
                'title' => 'Ibanez RG550 Genesis',
                'category_name' => 'Гитар',
                'description' => 'Жинхэнэ Япон чанар. Биедээ маш жижиг зураастай боловч дуугаралт болон тоглолтод ямар ч асуудалгүй.',
                'price' => '2,100,000 ₮',
                'condition' => 'Дунд',
                'conditionColor' => 'bg-orange-100 text-orange-700',
                'isUsed' => 'Хэрэглэсэн',
                'isVerified' => false,
                'img' => 'https://images.unsplash.com/photo-1516924962500-29390422550b?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1516924962500-29390422550b?w=1000',
                    'https://images.unsplash.com/photo-1605020420620-20c943cc4669?w=1000',
                    'https://images.unsplash.com/photo-1556449895-a33c9fac3300?w=1000',
                    'https://images.unsplash.com/photo-1561777848-6a56e08d6a26?w=1000'
                ],
                'weight' => 3.2,
                'size_category' => 'medium',
                'specs' => [
                    'Брэнд' => 'Ibanez',
                    'Загвар' => 'RG550 Genesis Collection',
                    'Үйлдвэр улс' => 'Япон (Japan)',
                    'Бриж (Bridge)' => 'Edge Tremolo',
                    'Өнгө' => 'Desert Sun Yellow'
                ]
            ],

            // ТӨГӨЛДӨР ХУУР
            [
                'title' => 'Yamaha C3 Grand Piano',
                'category_name' => 'Төгөлдөр хуур',
                'description' => 'Мэргэжлийн хөгжимчинд болон сургалтын төвд тохиромжтой. Жил бүр тогтмол хөгнөө хийлгэж байсан.',
                'price' => '12,500,000 ₮',
                'condition' => 'Маш сайн',
                'conditionColor' => 'bg-primary/10 text-primary',
                'isUsed' => 'Хэрэглэсэн',
                'isVerified' => true,
                'img' => 'https://images.unsplash.com/photo-1552422535-c45813c61732?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1552422535-c45813c61732?w=1000',
                    'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?w=1000',
                    'https://images.unsplash.com/photo-1571974599782-87624638275e?w=1000'
                ],
                'weight' => 320.0,
                'size_category' => 'large',
                'specs' => [
                    'Брэнд' => 'Yamaha',
                    'Загвар' => 'C3 Conservatory',
                    'Төрөл' => 'Grand Piano (6 хөлт)',
                    'Урт' => '186 см (6\'1")',
                    'Өнгө' => 'Polished Ebony (Хар гялгар)'
                ]
            ],
            [
                'title' => 'Roland FP-30X Digital Piano',
                'category_name' => 'Төгөлдөр хуур',
                'description' => null, // ХООСОН ҮЛДЭЭВ
                'price' => '2,800,000 ₮',
                'condition' => 'Шинэ',
                'conditionColor' => 'bg-blue-100 text-blue-700',
                'isUsed' => 'Битүүмжилсэн',
                'isVerified' => true,
                'img' => 'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?w=1000',
                    'https://images.unsplash.com/photo-1588690153255-a083f054baeb?w=1000',
                    'https://images.unsplash.com/photo-1601312384661-8280f58b0f44?w=1000'
                ],
                'weight' => 14.8,
                'size_category' => 'medium',
                'specs' => [
                    'Брэнд' => 'Roland',
                    'Загвар' => 'FP-30X',
                    'Даралт' => '88-key PHA-4 Standard Keyboard',
                    'Холболт' => 'Bluetooth Audio/MIDI'
                ]
            ],

            // БӨМБӨР
            [
                'title' => 'Pearl Export Drum Set',
                'category_name' => 'Бөмбөр',
                'description' => 'Бүх цан, хэнгэрэг бүрэн бүтэн. Тоглоход шууд бэлэн. Sabian цан дагалдана.',
                'price' => '3,900,000 ₮',
                'condition' => 'Дунд',
                'conditionColor' => 'bg-orange-100 text-orange-700',
                'isUsed' => 'Хэрэглэсэн',
                'isVerified' => false,
                'img' => 'https://images.unsplash.com/photo-1519892300165-cb5542fb47c7?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1519892300165-cb5542fb47c7?w=1000',
                    'https://images.unsplash.com/photo-1543443258-92b04ad5ecf5?w=1000',
                    'https://images.unsplash.com/photo-1595188812613-255d3f27f0da?w=1000'
                ],
                'weight' => 45.0,
                'size_category' => 'large',
                'specs' => [
                    'Брэнд' => 'Pearl',
                    'Загвар' => 'Export EXX',
                    'Хэсгийн тоо' => '5-piece',
                    'Цан (Cymbals)' => 'Sabian B8X дагалдана'
                ]
            ],
            [
                'title' => 'Roland V-Drums TD-17KVX',
                'category_name' => 'Бөмбөр',
                'description' => null, // ХООСОН ҮЛДЭЭВ
                'price' => '5,500,000 ₮',
                'condition' => 'Маш сайн',
                'conditionColor' => 'bg-primary/10 text-primary',
                'isUsed' => 'Хэрэглэсэн',
                'isVerified' => true,
                'img' => 'https://images.unsplash.com/photo-1543443258-92b04ad5ecf5?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1543443258-92b04ad5ecf5?w=1000',
                    'https://images.unsplash.com/photo-1519892300165-cb5542fb47c7?w=1000',
                    'https://images.unsplash.com/photo-1524230659092-07f99a75c013?w=1000'
                ],
                'weight' => 24.5,
                'size_category' => 'large',
                'specs' => [
                    'Брэнд' => 'Roland',
                    'Төрөл' => 'Цахилгаан бөмбөр (Electronic)',
                    'Модуль' => 'TD-17',
                    'Холболт' => 'Bluetooth, USB, MIDI'
                ]
            ],

            // ҮЛЭЭВЭР
            [
                'title' => 'Yamaha YAS-280 Saxophone',
                'category_name' => 'Үлээвэр',
                'description' => 'Анхлан суралцагчдад зориулагдсан дэлхийн хамгийн шилдэг саксофон. Цоо шинээрээ байгаа.',
                'price' => '4,100,000 ₮',
                'condition' => 'Шинэ',
                'conditionColor' => 'bg-blue-100 text-blue-700',
                'isUsed' => 'Битүүмжилсэн',
                'isVerified' => true,
                'img' => 'https://images.unsplash.com/photo-1573871666457-7c7329118cf9?w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1573871666457-7c7329118cf9?w=1000',
                    'https://images.unsplash.com/photo-1563854121307-e0704400e9bc?w=1000',
                    'https://images.unsplash.com/photo-1610488981442-99f57ebbf72f?w=1000'
                ],
                'weight' => 2.5,
                'size_category' => 'small',
                'specs' => [
                    'Брэнд' => 'Yamaha',
                    'Загвар' => 'YAS-280 (Alto)',
                    'Төрөл' => 'Сурагч/Эхлэн суралцагч'
                ]
            ],

            // ХИЙЛ
            [
                'title' => 'Yamaha V5SC Сонгодог хийл 4/4',
                'category_name' => 'Хийл',
                'description' => '4/4 хэмжээтэй, дуугаралт маш цэвэрхэн. Кэйс болон дагалдах хэрэгслүүдтэйгээ хамт зарна.',
                'price' => '1,200,000 ₮',
                'condition' => 'Шинэ',
                'conditionColor' => 'bg-blue-100 text-blue-700',
                'isUsed' => 'Шинэ',
                'isVerified' => true,
                'img' => 'https://images.unsplash.com/photo-1612225330812-01a9c6b355ec?auto=format&fit=crop&q=80&w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1612225330812-01a9c6b355ec?w=1000',
                    'https://images.unsplash.com/photo-1460036521480-c4b50fd03d27?w=1000',
                    'https://images.unsplash.com/photo-1596700518337-f82b82650ab3?w=1000'
                ],
                'weight' => 1.2,
                'size_category' => 'small',
                'specs' => [
                    'Брэнд' => 'Yamaha',
                    'Хэмжээ' => '4/4 (Бүрэн хэмжээ)',
                    'Нүүрэн тал' => 'Spruce (Гацуур)',
                    'Дагалдах' => 'Кэйс, нум, давирхай'
                ]
            ],
            [
                'title' => 'Цахилгаан хийл (Silent Violin)',
                'category_name' => 'Хийл',
                'description' => null, // ХООСОН ҮЛДЭЭВ
                'price' => '450,000 ₮',
                'condition' => 'Дунд',
                'conditionColor' => 'bg-orange-100 text-orange-700',
                'isUsed' => 'Хэрэглэсэн',
                'isVerified' => false,
                'img' => 'https://images.unsplash.com/photo-1600189020942-0fdb8232fc18?auto=format&fit=crop&q=80&w=800',
                'images' => [
                    'https://images.unsplash.com/photo-1600189020942-0fdb8232fc18?w=1000',
                    'https://images.unsplash.com/photo-1460036521480-c4b50fd03d27?w=1000',
                    'https://images.unsplash.com/photo-1574163901614-25e2eeb62f3f?w=1000',
                    'https://images.unsplash.com/photo-1612225330812-01a9c6b355ec?w=1000'
                ],
                'weight' => 0.9,
                'size_category' => 'small',
                'specs' => [
                    'Төрөл' => 'Цахилгаан (Solid body)',
                    'Хэмжээ' => '4/4',
                    'Холболт' => 'Чихэвч, Өсгөгч (Amp)',
                    'Тэжээл' => '9V Батерей',
                ]
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
