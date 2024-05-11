<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
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
            'full_name' => 'Yusuf',
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
                'name' => 'Mobiles',
                'image_link' => 'https://example.com/mobiles.jpg',
                'subcategories' => [
                    [
                        'name' => 'Smartphones',
                        'image_link' => 'https://example.com/smartphones.jpg',
                    ],
                    [
                        'name' => 'Feature phones',
                        'image_link' => 'https://example.com/feature_phones.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Electronics',
                'image_link' => 'https://example.com/electronics.jpg',
                'subcategories' => [
                    [
                        'name' => 'Computers & Laptops',
                        'image_link' => 'https://example.com/computers_laptops.jpg',
                    ],
                    [
                        'name' => 'Tablets & E-readers',
                        'image_link' => 'https://example.com/tablets_ereaders.jpg',
                    ],
                    [
                        'name' => 'Cameras & Photography',
                        'image_link' => 'https://example.com/cameras_photography.jpg',
                    ],
                    [
                        'name' => 'Audio & Headphones',
                        'image_link' => 'https://example.com/audio_headphones.jpg',
                    ],
                    [
                        'name' => 'TVs & Home Theater',
                        'image_link' => 'https://example.com/tvs_home_theater.jpg',
                    ],
                    [
                        'name' => 'Video Games & Consoles',
                        'image_link' => 'https://example.com/video_games_consoles.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Vehicles',
                'image_link' => 'https://example.com/vehicles.jpg',
                'subcategories' => [
                    [
                        'name' => 'Cars',
                        'image_link' => 'https://example.com/cars.jpg',
                    ],
                    [
                        'name' => 'Motorcycles',
                        'image_link' => 'https://example.com/motorcycles.jpg',
                    ],
                    [
                        'name' => 'Trucks & Commercial Vehicles',
                        'image_link' => 'https://example.com/trucks_commercial_vehicles.jpg',
                    ],
                    [
                        'name' => 'Boats & Watercraft',
                        'image_link' => 'https://example.com/boats_watercraft.jpg',
                    ],
                    [
                        'name' => 'RVs & Campers',
                        'image_link' => 'https://example.com/rvs_campers.jpg',
                    ],
                    [
                        'name' => 'Parts & Accessories',
                        'image_link' => 'https://example.com/parts_accessories.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Property',
                'image_link' => 'https://example.com/property.jpg',
                'subcategories' => [
                    [
                        'name' => 'Houses & Apartments for Sale',
                        'image_link' => 'https://example.com/houses_apartments_for_sale.jpg',
                    ],
                    [
                        'name' => 'Houses & Apartments for Rent',
                        'image_link' => 'https://example.com/houses_apartments_for_rent.jpg',
                    ],
                    [
                        'name' => 'Commercial Property',
                        'image_link' => 'https://example.com/commercial_property.jpg',
                    ],
                    [
                        'name' => 'Land & Plots',
                        'image_link' => 'https://example.com/land_plots.jpg',
                    ],
                    [
                        'name' => 'Vacation Rentals',
                        'image_link' => 'https://example.com/vacation_rentals.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Living',
                'image_link' => 'https://example.com/living.jpg',
                'subcategories' => [
                    [
                        'name' => 'Furniture',
                        'image_link' => 'https://example.com/furniture.jpg',
                    ],
                    [
                        'name' => 'Home Appliances',
                        'image_link' => 'https://example.com/home_appliances.jpg',
                    ],
                    [
                        'name' => 'Home Decor & Garden',
                        'image_link' => 'https://example.com/home_decor_garden.jpg',
                    ],
                    [
                        'name' => 'Kitchen & Dining',
                        'image_link' => 'https://example.com/kitchen_dining.jpg',
                    ],
                    [
                        'name' => 'Bedding & Linens',
                        'image_link' => 'https://example.com/bedding_linens.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Pets',
                'image_link' => 'https://example.com/pets.jpg',
                'subcategories' => [
                    [
                        'name' => 'Dogs',
                        'image_link' => 'https://example.com/dogs.jpg',
                    ],
                    [
                        'name' => 'Cats',
                        'image_link' => 'https://example.com/cats.jpg',
                    ],
                    [
                        'name' => 'Birds',
                        'image_link' => 'https://example.com/birds.jpg',
                    ],
                    [
                        'name' => 'Fish & Aquariums',
                        'image_link' => 'https://example.com/fish_aquariums.jpg',
                    ],
                    [
                        'name' => 'Pet Supplies & Accessories',
                        'image_link' => 'https://example.com/pet_supplies_accessories.jpg',
                    ],
                ],
            ],
            [
                'name' => "Men's Fashion",
                'image_link' => 'https://example.com/mens_fashion.jpg',
                'subcategories' => [
                    [
                        'name' => 'Men Clothing',
                        'image_link' => 'https://example.com/mens_clothing.jpg',
                    ],
                    [
                        'name' => 'Shoes',
                        'image_link' => 'https://example.com/mens_shoes.jpg',
                    ],
                ],
            ],
            [
                'name' => "Women's Fashion",
                'image_link' => 'https://example.com/womens_fashion.jpg',
                'subcategories' => [
                    [
                        'name' => 'Women Clothing',
                        'image_link' => 'https://example.com/womens_clothing.jpg',
                    ],
                    [
                        'name' => 'Women Shoes',
                        'image_link' => 'https://example.com/womens_shoes.jpg',
                    ],
                    [
                        'name' => 'Accessories',
                        'image_link' => 'https://example.com/womens_accessories.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Sports & Kids',
                'image_link' => 'https://example.com/sports_kids.jpg',
                'subcategories' => [
                    [
                        'name' => 'Sports Equipment',
                        'image_link' => 'https://example.com/sports_equipment.jpg',
                    ],
                    [
                        'name' => 'Fitness & Exercise',
                        'image_link' => 'https://example.com/fitness_exercise.jpg',
                    ],
                    [
                        'name' => 'Outdoor & Camping',
                        'image_link' => 'https://example.com/outdoor_camping.jpg',
                    ],
                    [
                        'name' => 'Toys & Games',
                        'image_link' => 'https://example.com/toys_games.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Business',
                'image_link' => 'https://example.com/business.jpg',
                'subcategories' => [
                    [
                        'name' => 'Business for Sale',
                        'image_link' => 'https://example.com/business_for_sale.jpg',
                    ],
                    [
                        'name' => 'Office Equipment & Furniture',
                        'image_link' => 'https://example.com/office_equipment_furniture.jpg',
                    ],
                    [
                        'name' => 'Services',
                        'image_link' => 'https://example.com/services.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Essentials',
                'image_link' => 'https://example.com/essentials.jpg',
                'subcategories' => [
                    [
                        'name' => 'Groceries & Food',
                        'image_link' => 'https://example.com/groceries_food.jpg',
                    ],
                    [
                        'name' => 'Health & Beauty',
                        'image_link' => 'https://example.com/health_beauty.jpg',
                    ],
                    [
                        'name' => 'Baby & Kids Essentials',
                        'image_link' => 'https://example.com/baby_kids_essentials.jpg',
                    ],
                    [
                        'name' => 'Cleaning & Household',
                        'image_link' => 'https://example.com/cleaning_household.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Education',
                'image_link' => 'https://example.com/education.jpg',
                'subcategories' => [
                    [
                        'name' => 'Textbooks & Study Materials',
                        'image_link' => 'https://example.com/textbooks_study_materials.jpg',
                    ],
                    [
                        'name' => 'Online Courses & Tutorials',
                        'image_link' => 'https://example.com/online_courses_tutorials.jpg',
                    ],
                    [
                        'name' => 'School Supplies',
                        'image_link' => 'https://example.com/school_supplies.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Agriculture',
                'image_link' => 'https://example.com/agriculture.jpg',
                'subcategories' => [
                    [
                        'name' => 'Farm Equipment & Machinery',
                        'image_link' => 'https://example.com/farm_equipment_machinery.jpg',
                    ],
                    [
                        'name' => 'Livestock',
                        'image_link' => 'https://example.com/livestock.jpg',
                    ],
                    [
                        'name' => 'Seeds & Plants',
                        'image_link' => 'https://example.com/seeds_plants.jpg',
                    ],
                    [
                        'name' => 'Agricultural Services',
                        'image_link' => 'https://example.com/agricultural_services.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Jobs',
                'image_link' => 'https://example.com/jobs.jpg',
                'subcategories' => [
                    [
                        'name' => 'Job Openings',
                        'image_link' => 'https://example.com/job_openings.jpg',
                    ],
                    [
                        'name' => 'Freelance & Part-Time',
                        'image_link' => 'https://example.com/freelance_part_time.jpg',
                    ],
                    [
                        'name' => 'Internships',
                        'image_link' => 'https://example.com/internships.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Services',
                'image_link' => 'https://example.com/services.jpg',
                'subcategories' => [
                    [
                        'name' => 'Home Services',
                        'image_link' => 'https://example.com/home_services.jpg',
                    ],
                    [
                        'name' => 'Beauty & Wellness',
                        'image_link' => 'https://example.com/beauty_wellness.jpg',
                    ],
                    [
                        'name' => 'Tutoring & Lessons',
                        'image_link' => 'https://example.com/tutoring_lessons.jpg',
                    ],
                    [
                        'name' => 'Event Planning & Catering',
                        'image_link' => 'https://example.com/event_planning_catering.jpg',
                    ],
                ],
            ],
            [
                'name' => 'Overseas',
                'image_link' => 'https://example.com/overseas.jpg',
                'subcategories' => [
                    [
                        'name' => 'International Real Estate',
                        'image_link' => 'https://example.com/international_real_estate.jpg',
                    ],
                    [
                        'name' => 'International Jobs',
                        'image_link' => 'https://example.com/international_jobs.jpg',
                    ],
                    [
                        'name' => 'Travel & Vacation Rentals',
                        'image_link' => 'https://example.com/travel_vacation_rentals.jpg',
                    ],
                    [
                        'name' => 'Import & Export',
                        'image_link' => 'https://example.com/import_export.jpg',
                    ],
                ],
            ],
        ];

        foreach ($categories as $category) {
            $this->createCategoryWithSubcategories($category['name'], $category['subcategories']);
        }

        $this->createProduct();
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

    public function createProduct()
    {
        $featureText = "Super Retina XDR Display: Experience stunning clarity and vivid colors on the ProMotion OLED display, with an adaptive refresh rate for smooth scrolling and responsiveness.
A15 Bionic Chip: Enjoy lightning-fast performance and efficiency, powering advanced computational photography, gaming, and augmented reality experiences.
Pro Camera System: Capture professional-quality photos and videos with the triple-camera setup, including an enhanced ultra-wide lens and improved low-light performance.
Cinematic Mode: Elevate your storytelling with cinematic-style videos, utilizing rack focus and depth of field effects for immersive content creation.
ProRAW and ProRes Video: Unleash your creativity with advanced photo and video formats, offering unparalleled control and flexibility in post-production editing.
5G Connectivity: Stay connected at high speeds with 5G capabilities, enabling faster downloads, smoother streaming, and enhanced gaming experiences.
Longer Battery Life: Power through your day with confidence, thanks to optimized battery life and efficient power management features.
ProMotion Technology: Enjoy a smoother and more responsive user experience, with adaptive refresh rates up to 120Hz for fluid interactions and scrolling.
Enhanced Privacy and Security: Protect your personal information with advanced security features, including Face ID and secure enclave technology.
Sleek and Durable Design: Crafted from durable materials and featuring a stylish, edge-to-edge design, the iPhone 13 Pro is as beautiful as it is powerful, making it the perfect companion for your everyday adventures.";
        Category::all()->each(function ($category) use ($featureText) {
            // Loop through each category's subcategories
            $category->sub_category->each(function ($sub_category) use ($category, $featureText) {
                for ($i = 1; $i <= 10; $i++) {
                    Product::create([
                        'user_id' => 2,
                        'category_id' => $category->id,
                        'sub_category_id' => $sub_category->id,
                        'slug' => "sample-product-$i",
                        'title' => "$sub_category->name Product $i",
                        'size' => 'Medium', // Sample size
                        'color' => 'Red', // Sample color
                        'location' => 'Sample Location',
                        'condition' => 'New', // Sample condition
                        'brand' => 'Sample Brand',
                        'edition' => 'Standard', // Sample edition
                        'product_type' => 'normal', // Sample product type
                        'priority' => $i, // Sample priority
                        'expired_at' => now()->addDays(30), // Sample expiration date
                        'authenticity' => 'Original', // Sample authenticity
                        'features' => $featureText, // Sample features
                        'division_id' => 1, // Sample division ID
                        'district_id' => 1, // Sample district ID
                        'sub_district_id' => 1, // Sample sub-district ID
                        'view' => 0, // Initial view count
                        'status' => 'approved', // Sample status
                        'points' => 0.0, // Initial points
                        'price' => 100.0, // Sample price
                        'contact_name' => 'John Doe', // Sample contact name
                        'contact_email' => 'john@example.com', // Sample contact email
                        'contact_number' => '+1234567890', // Sample contact number
                        'additional_contact_number' => '+0987654321', // Sample additional contact number
                        'show_contact_number' => true, // Sample show contact number flag
                        'accept_terms' => true, // Sample accept terms flag
                    ]);
                }
            });
        });

        Product::all()->each(function ($product) {
            for ($i = 0; $i < 4 ; $i++) {
                ProductImage::query()->create([
                    'product_id' => $product->id,
                    'image' => "https://cdn.vuetifyjs.com/images/cards/cooking.png"
                ]);
            }
        });
    }
}
