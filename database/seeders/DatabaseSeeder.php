<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\UserWallet;
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
            'phone' => '01815625376',
            'website' => 'https://binnur.xyz',
            'company' => 'AdBarta',
            'about' => 'This is about',
            'role' => 'super_admin',
            'password' => bcrypt('password'),
            'status' => 'approved',
        ]);
        $user = User::query()->create([
            'full_name' => 'Ad Barta',
            'email' => 'binnur@tikweb.com',
            'uid' => Str::uuid(),
            'photo' => '',
            'phone' => '01815625375',
            'website' => 'https://binnur.xyz',
            'company' => 'AdBarta',
            'about' => 'This is about',
            'role' => 'seller',
            'password' => bcrypt('password'),
            'status' => 'pending',
        ]);

        $users = User::query()->get();
        foreach ($users as $item) {
            UserWallet::query()->create([
                'user_id' => $item->id,
                'available' => 0,
                'used' => 0,
            ]);
        }
        $categories = [
            [
                'name' => 'Electronics',
                'image_link' => 'https://example.com/electronics.jpg',
                'subcategories' => [
                    [
                        'name' => 'Mobile Phones',
                        'image_link' => 'https://example.com/mobile_phones.jpg',
                    ],
                    [
                        'name' => 'Laptops',
                        'image_link' => 'https://example.com/laptops.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Clothing',
                'image_link' => 'https://example.com/clothing.jpg',
                'subcategories' => [
                    [
                        'name' => 'Men\'s Clothing',
                        'image_link' => 'https://example.com/mens_clothing.jpg',
                    ],
                    [
                        'name' => 'Women\'s Clothing',
                        'image_link' => 'https://example.com/womens_clothing.jpg',
                    ],
                ],
            ],
        ];

        foreach ($categories as $category) {
            $this->createCategoryWithSubcategories($category['name'], $category['subcategories']);
        }

    }

    function createCategoryWithSubcategories($name, $subcategories = [], $parentId = null)
    {
        if ($parentId) {
            $category = SubCategory::create([
                'name' => $name,
                'category_id' => $parentId,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU',
            ]);

        } else {
            $category = Category::create([
                'name' => $name,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU',
            ]);
        }
        foreach ($subcategories as $subcategory) {
            $this->createCategoryWithSubcategories($subcategory['name'], [], $category->id);
        }
    }
}
