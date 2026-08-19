<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LiveStream;
use App\Models\Offer;
use App\Models\Store;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'email' => 'admin@liveoffers.com',
            'phone' => '0790000000',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create Store Owner User
        $storeOwner = User::create([
            'username' => 'storeowner',
            'password' => Hash::make('store123'),
            'email' => 'owner@store.com',
            'phone' => '0791111111',
            'role' => 'store_owner',
            'is_active' => true,
        ]);

        // Create Categories
        $electronics = Category::create([
            'name' => 'إلكترونيات',
            'icon' => '📱',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $fashion = Category::create([
            'name' => 'أزياء',
            'icon' => '👕',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $food = Category::create([
            'name' => 'طعام ومشروبات',
            'icon' => '🍔',
            'sort_order' => 3,
            'is_active' => true,
        ]);

        $home = Category::create([
            'name' => 'منزل ومطبخ',
            'icon' => '🏠',
            'sort_order' => 4,
            'is_active' => true,
        ]);

        // Create Subcategories
        Subcategory::create(['category_id' => $electronics->id, 'name' => 'هواتف ذكية', 'is_active' => true]);
        Subcategory::create(['category_id' => $electronics->id, 'name' => 'لابتوبات', 'is_active' => true]);
        Subcategory::create(['category_id' => $electronics->id, 'name' => 'سماعات', 'is_active' => true]);

        Subcategory::create(['category_id' => $fashion->id, 'name' => 'ملابس رجالية', 'is_active' => true]);
        Subcategory::create(['category_id' => $fashion->id, 'name' => 'ملابس نسائية', 'is_active' => true]);
        Subcategory::create(['category_id' => $fashion->id, 'name' => 'أحذية', 'is_active' => true]);

        Subcategory::create(['category_id' => $food->id, 'name' => 'وجبات سريعة', 'is_active' => true]);
        Subcategory::create(['category_id' => $food->id, 'name' => 'حلويات', 'is_active' => true]);

        Subcategory::create(['category_id' => $home->id, 'name' => 'أثاث', 'is_active' => true]);
        Subcategory::create(['category_id' => $home->id, 'name' => 'أدوات مطبخ', 'is_active' => true]);

        // Create Store
        $store = Store::create([
            'owner_id' => $storeOwner->id,
            'name' => 'متجر العروض الذهبية',
            'description' => 'متجركم الأول لأفضل العروض والخصومات في المدينة. نقدم لكم منتجات عالية الجودة بأسعار لا تقاوم.',
            'logo' => null,
            'address' => 'شارع الملك حسين، وسط البلد، عمّان',
            'latitude' => 31.9454,
            'longitude' => 35.9284,
            'phone' => '0791111111',
            'whatsapp_number' => '962791111111',
            'is_active' => true,
        ]);

        // Create Offers
        Offer::create([
            'store_id' => $store->id,
            'category_id' => $electronics->id,
            'subcategory_id' => 1,
            'title' => 'هاتف ذكي رائد - عرض خاص',
            'description' => 'هاتف ذكي بمواصفات عالية، شاشة 6.5 بوصة، كاميرا 48 ميجابكسل، بطارية 5000 مل أمبير. عرض محدود بسعر لا يقاوم!',
            'original_price' => 350.00,
            'offer_price' => 249.00,
            'image' => null,
            'is_active' => true,
            'is_featured' => true,
            'is_ai_generated' => false,
            'view_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
        ]);

        Offer::create([
            'store_id' => $store->id,
            'category_id' => $electronics->id,
            'subcategory_id' => 3,
            'title' => 'سماعات بلوتوث لاسلكية',
            'description' => 'سماعات بلوتوث لاسلكية بجودة صوت عالية، عزل ضوضاء نشط، بطارية تدوم 30 ساعة. الخصم يصل إلى 40%!',
            'original_price' => 80.00,
            'offer_price' => 49.00,
            'image' => null,
            'is_active' => true,
            'is_featured' => false,
            'is_ai_generated' => false,
            'view_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
        ]);

        Offer::create([
            'store_id' => $store->id,
            'category_id' => $fashion->id,
            'subcategory_id' => 4,
            'title' => 'قميص رجالي كلاسيك',
            'description' => 'قميص رجالي كلاسيك بقماش قطني عالي الجودة، متوفر بمقاسات متعددة. عرض خاص لفترة محدودة!',
            'original_price' => 45.00,
            'offer_price' => 29.00,
            'image' => null,
            'is_active' => true,
            'is_featured' => true,
            'is_ai_generated' => false,
            'view_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(10),
        ]);

        Offer::create([
            'store_id' => $store->id,
            'category_id' => $food->id,
            'subcategory_id' => 7,
            'title' => 'وجبة عائلية - عرض الجمعة',
            'description' => 'وجبة عائلية كاملة تكفي 4 أشخاص. تشمل برجر، بطاطس، ومشروبات. عرض خاص ليوم الجمعة فقط!',
            'original_price' => 25.00,
            'offer_price' => 15.00,
            'image' => null,
            'is_active' => true,
            'is_featured' => false,
            'is_ai_generated' => false,
            'view_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(3),
        ]);

        Offer::create([
            'store_id' => $store->id,
            'category_id' => $home->id,
            'subcategory_id' => 9,
            'title' => 'طقم أواني طبخ 10 قطع',
            'description' => 'طقم أواني طبخ من الستانلس ستيل عالي الجودة، 10 قطع، مناسب لجميع أنواع المواقد. خصم 50%!',
            'original_price' => 120.00,
            'offer_price' => 60.00,
            'image' => null,
            'is_active' => true,
            'is_featured' => true,
            'is_ai_generated' => false,
            'view_count' => 0,
            'start_date' => now(),
            'end_date' => now()->addDays(14),
        ]);

        // Create an active live stream
        LiveStream::create([
            'store_id' => $store->id,
            'channel_name' => 'store_1_' . time(),
            'agora_token' => 'demo_token',
            'preview_image' => null,
            'max_viewers' => 0,
            'is_active' => true,
            'started_at' => now(),
        ]);
    }
}