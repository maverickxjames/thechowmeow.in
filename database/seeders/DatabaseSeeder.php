<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Banner;
use App\Models\Page;
use App\Models\Coupon;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===== USERS =====
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@petwear.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'phone' => '9876543210',
        ]);

        $customers = [];
        $customerData = [
            ['name' => 'Priya Sharma', 'email' => 'priya@test.com'],
            ['name' => 'Raj Patel', 'email' => 'raj@test.com'],
            ['name' => 'Anita Kumar', 'email' => 'anita@test.com'],
            ['name' => 'Vikram Singh', 'email' => 'vikram@test.com'],
            ['name' => 'Meera Joshi', 'email' => 'meera@test.com'],
        ];
        foreach ($customerData as $cd) {
            $customers[] = User::create(array_merge($cd, [
                'password' => Hash::make('password'),
                'phone' => '98' . rand(10000000, 99999999),
            ]));
        }

        // ===== ROOT CATEGORIES =====
        $dog = Category::create(['name' => 'Dog', 'slug' => 'dog', 'description' => 'Stylish clothing for your adorable dogs', 'sort_order' => 1]);
        $cat = Category::create(['name' => 'Cat', 'slug' => 'cat', 'description' => 'Fashionable outfits for your lovely cats', 'sort_order' => 2]);
        $accessories = Category::create(['name' => 'Accessories', 'slug' => 'accessories', 'description' => 'Complete the look with premium accessories', 'sort_order' => 3]);
        $tcm = Category::create(['name' => 'TCM Special', 'slug' => 'tcm-special', 'description' => 'Special collections for every occasion', 'sort_order' => 4]);
        $sale = Category::create(['name' => 'Sale', 'slug' => 'sale', 'description' => 'Grab the best deals on pet clothing', 'sort_order' => 5]);

        // ===== SUB-CATEGORIES =====
        // Dog & Cat sub-categories
        foreach ([$dog, $cat] as $parentCat) {
            $casual = Category::create(['name' => 'Casual Wear', 'slug' => $parentCat->slug . '-casual-wear', 'parent_id' => $parentCat->id, 'sort_order' => 1]);
            Category::create(['name' => 'Shirt', 'slug' => $parentCat->slug . '-shirt', 'parent_id' => $casual->id, 'sort_order' => 1]);
            Category::create(['name' => 'Dress', 'slug' => $parentCat->slug . '-dress', 'parent_id' => $casual->id, 'sort_order' => 2]);

            $birthday = Category::create(['name' => 'Birthday Wear', 'slug' => $parentCat->slug . '-birthday-wear', 'parent_id' => $parentCat->id, 'sort_order' => 2]);
            Category::create(['name' => 'Tuxedo', 'slug' => $parentCat->slug . '-tuxedo', 'parent_id' => $birthday->id, 'sort_order' => 1]);
            Category::create(['name' => 'Fancy Dress', 'slug' => $parentCat->slug . '-fancy-dress', 'parent_id' => $birthday->id, 'sort_order' => 2]);

            $festive = Category::create(['name' => 'Festive Wear', 'slug' => $parentCat->slug . '-festive-wear', 'parent_id' => $parentCat->id, 'sort_order' => 3]);
            Category::create(['name' => 'Kurta', 'slug' => $parentCat->slug . '-kurta', 'parent_id' => $festive->id, 'sort_order' => 1]);
            Category::create(['name' => 'Saree', 'slug' => $parentCat->slug . '-saree', 'parent_id' => $festive->id, 'sort_order' => 2]);
            Category::create(['name' => 'Sherwani', 'slug' => $parentCat->slug . '-sherwani', 'parent_id' => $festive->id, 'sort_order' => 3]);
            Category::create(['name' => 'Lehenga', 'slug' => $parentCat->slug . '-lehenga', 'parent_id' => $festive->id, 'sort_order' => 4]);
        }

        // Accessories sub-categories
        $accItems = ['Bandana', 'Bow', 'Caps', 'Necklace', 'Sunglasses'];
        foreach ($accItems as $i => $name) {
            Category::create(['name' => $name, 'slug' => Str::slug($name), 'parent_id' => $accessories->id, 'sort_order' => $i + 1]);
        }

        // TCM sub-categories
        $tcmItems = ['Valentine Collection', 'Eid Collection', 'Christmas Collection', 'Winter Wear'];
        foreach ($tcmItems as $i => $name) {
            Category::create(['name' => $name, 'slug' => Str::slug($name), 'parent_id' => $tcm->id, 'sort_order' => $i + 1]);
        }

        // Sale sub-categories
        $saleItems = ['XS', 'S/M', 'L', 'XL-3XL'];
        foreach ($saleItems as $i => $name) {
            Category::create(['name' => 'Size ' . $name, 'slug' => 'sale-' . Str::slug($name), 'parent_id' => $sale->id, 'sort_order' => $i + 1]);
        }

        // ===== PRODUCTS =====
        $products = [
            ['name' => 'Pawsome Cotton Polo', 'desc' => 'Classic cotton polo shirt for your stylish pup.', 'price' => 599, 'cats' => [$dog->id], 'featured' => true],
            ['name' => 'Royal Velvet Tuxedo', 'desc' => 'Make your pet the star of any birthday party.', 'price' => 1299, 'cats' => [$dog->id], 'featured' => true],
            ['name' => 'Floral Summer Dress', 'desc' => 'Lightweight floral dress perfect for summer walks.', 'price' => 799, 'cats' => [$dog->id, $cat->id], 'featured' => true],
            ['name' => 'Diwali Sherwani', 'desc' => 'Traditional sherwani with golden embroidery.', 'price' => 1499, 'cats' => [$dog->id], 'featured' => true],
            ['name' => 'Festive Lehenga Set', 'desc' => 'Beautiful lehenga set for your princess pet.', 'price' => 1699, 'cats' => [$cat->id], 'featured' => true],
            ['name' => 'Cozy Winter Hoodie', 'desc' => 'Warm fleece hoodie for cold winter nights.', 'price' => 899, 'cats' => [$dog->id, $cat->id], 'featured' => false],
            ['name' => 'Silk Bandana Set (3pc)', 'desc' => 'Premium silk bandanas in 3 trendy colors.', 'price' => 399, 'cats' => [$accessories->id], 'featured' => true],
            ['name' => 'Party Bow Tie', 'desc' => 'Cute bow tie for special occasions.', 'price' => 249, 'cats' => [$accessories->id], 'featured' => false],
            ['name' => 'Valentine Heart Sweater', 'desc' => 'Heart-patterned sweater for your valentine pet.', 'price' => 749, 'cats' => [$tcm->id, $dog->id], 'featured' => true],
            ['name' => 'Christmas Elf Costume', 'desc' => 'Turn your pet into Santa\'s cutest little helper!', 'price' => 999, 'cats' => [$tcm->id], 'featured' => false],
            ['name' => 'Designer Cat Kurta', 'desc' => 'Handcrafted kurta for your festive feline.', 'price' => 699, 'cats' => [$cat->id], 'featured' => true],
            ['name' => 'Sporty Raincoat', 'desc' => 'Waterproof raincoat to keep your pet dry.', 'price' => 849, 'cats' => [$dog->id, $cat->id], 'featured' => false],
            ['name' => 'Glitter Party Cape', 'desc' => 'Sparkly cape for parties and photoshoots.', 'price' => 549, 'cats' => [$dog->id, $cat->id], 'featured' => false],
            ['name' => 'Pet Aviator Sunglasses', 'desc' => 'Cool aviator sunglasses with UV protection.', 'price' => 349, 'cats' => [$accessories->id], 'featured' => false],
            ['name' => 'Eid Embroidered Vest', 'desc' => 'Beautifully embroidered vest for Eid celebrations.', 'price' => 1199, 'cats' => [$tcm->id, $dog->id], 'featured' => false],
            ['name' => 'Striped Sailor Tee', 'desc' => 'Navy striped tee for nautical vibes.', 'price' => 499, 'cats' => [$dog->id, $cat->id, $sale->id], 'featured' => false],
            ['name' => 'Pearl Necklace for Pets', 'desc' => 'Elegant faux pearl necklace.', 'price' => 299, 'cats' => [$accessories->id], 'featured' => false],
            ['name' => 'Denim Jacket', 'desc' => 'Trendy denim jacket with cute patches.', 'price' => 1099, 'cats' => [$dog->id], 'featured' => false],
            ['name' => 'Tutu Princess Skirt', 'desc' => 'Adorable tutu skirt for your pet princess.', 'price' => 449, 'cats' => [$cat->id, $dog->id], 'featured' => false],
            ['name' => 'Winter Wool Cap', 'desc' => 'Warm wool cap with ear holes.', 'price' => 299, 'cats' => [$accessories->id], 'featured' => false],
        ];

        $sizes = ['XS', 'S', 'M', 'L', 'XL'];
        $colors = ['Red', 'Blue', 'Black', 'Pink', 'White', 'Brown', 'Green'];

        foreach ($products as $index => $pData) {
            $product = Product::create([
                'name' => $pData['name'],
                'slug' => Str::slug($pData['name']),
                'description' => $pData['desc'] . "\n\nMade with premium quality materials. Machine washable. Adjustable fit for maximum comfort.\n\n• Soft breathable fabric\n• Easy to put on and take off\n• Available in multiple sizes\n• Perfect for everyday wear and special occasions",
                'short_description' => $pData['desc'],
                'base_price' => $pData['price'],
                'is_active' => true,
                'is_featured' => $pData['featured'],
                'meta_title' => $pData['name'] . ' | PetWear',
                'meta_description' => $pData['desc'],
                'views_count' => rand(10, 500),
            ]);

            $product->categories()->sync($pData['cats']);

            // Create variants
            $variantCount = rand(3, 5);
            $selectedSizes = array_slice($sizes, 0, $variantCount);
            $mainColor = $colors[array_rand($colors)];
            $secondColor = $colors[array_rand($colors)];

            foreach ($selectedSizes as $si => $size) {
                $color = $si % 2 === 0 ? $mainColor : $secondColor;
                $salePrice = $pData['featured'] ? round($pData['price'] * 0.85, 2) : null;

                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $size,
                    'color' => $color,
                    'sku' => 'PW-' . strtoupper(substr(Str::slug($pData['name']), 0, 6)) . '-' . $size . '-' . strtoupper(substr($color, 0, 2)),
                    'price' => $pData['price'],
                    'sale_price' => $salePrice,
                    'discount_percent' => $salePrice ? null : (rand(0, 3) === 0 ? rand(5, 20) : null),
                    'stock_quantity' => rand(0, 30),
                    'is_active' => true,
                ]);
            }
        }

        // ===== MENUS =====
        $headerMenu = Menu::create(['name' => 'Main Navigation', 'slug' => 'main-navigation', 'location' => 'header', 'is_active' => true]);

        $menuItems = [
            ['title' => 'Home', 'url' => '/', 'type' => 'custom', 'sort_order' => 0],
            ['title' => 'Dog', 'type' => 'category', 'linkable_type' => Category::class, 'linkable_id' => $dog->id, 'sort_order' => 1],
            ['title' => 'Cat', 'type' => 'category', 'linkable_type' => Category::class, 'linkable_id' => $cat->id, 'sort_order' => 2],
            ['title' => 'Accessories', 'type' => 'category', 'linkable_type' => Category::class, 'linkable_id' => $accessories->id, 'sort_order' => 3],
            ['title' => 'TCM Special', 'type' => 'category', 'linkable_type' => Category::class, 'linkable_id' => $tcm->id, 'sort_order' => 4],
            ['title' => 'Sale', 'type' => 'category', 'linkable_type' => Category::class, 'linkable_id' => $sale->id, 'sort_order' => 5],
        ];

        foreach ($menuItems as $mi) {
            MenuItem::create(array_merge($mi, ['menu_id' => $headerMenu->id, 'is_active' => true]));
        }

        // Add sub-menu items for all categories that have children
        $menuCategoryMap = [
            'Dog' => $dog,
            'Cat' => $cat,
            'Accessories' => $accessories,
            'TCM Special' => $tcm,
            'Sale' => $sale,
        ];

        foreach ($menuCategoryMap as $menuTitle => $parentCategory) {
            $menuItem = MenuItem::where('title', $menuTitle)->where('menu_id', $headerMenu->id)->first();
            if (!$menuItem) continue;

            $subCategories = Category::where('parent_id', $parentCategory->id)->orderBy('sort_order')->get();
            foreach ($subCategories as $i => $sub) {
                MenuItem::create([
                    'menu_id' => $headerMenu->id,
                    'parent_id' => $menuItem->id,
                    'title' => $sub->name,
                    'type' => 'category',
                    'linkable_type' => Category::class,
                    'linkable_id' => $sub->id,
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }

        // ===== BANNERS =====
        Banner::create(['title' => 'Dress Your Pet in Style 🐾', 'subtitle' => 'Premium clothing for dogs & cats. Free shipping on orders above ₹999.', 'image_path' => 'banners/placeholder.jpg', 'button_text' => 'Shop Now', 'button_url' => '/products', 'sort_order' => 1, 'is_active' => true]);
        Banner::create(['title' => 'Festive Collection 2025 ✨', 'subtitle' => 'Make your pets the star of every celebration with our exclusive festive range.', 'image_path' => 'banners/placeholder2.jpg', 'button_text' => 'Explore Collection', 'button_url' => '/category/tcm-special', 'sort_order' => 2, 'is_active' => true]);
        Banner::create(['title' => 'Sale Upto 40% Off 🏷️', 'subtitle' => 'Grab the best deals on pet clothing. Limited time offer!', 'image_path' => 'banners/placeholder3.jpg', 'button_text' => 'Shop Sale', 'button_url' => '/category/sale', 'sort_order' => 3, 'is_active' => true]);

        // ===== PAGES =====
        Page::create(['title' => 'About Us', 'slug' => 'about-us', 'content' => '<h2>Welcome to PetWear</h2><p>PetWear is India\'s premium pet clothing brand, dedicated to making your furry friends look and feel their best. Founded in 2024, we\'ve been creating stylish, comfortable, and high-quality clothing for dogs and cats.</p><h3>Our Mission</h3><p>To bring joy to pets and their parents through fashionable, comfortable, and sustainable pet clothing.</p><h3>Why Choose PetWear?</h3><ul><li>Premium quality fabrics</li><li>Designed for comfort</li><li>Machine washable</li><li>Wide size range (XS to 3XL)</li><li>Made in India 🇮🇳</li></ul>', 'is_active' => true]);
        Page::create(['title' => 'Privacy Policy', 'slug' => 'privacy-policy', 'content' => '<h2>Privacy Policy</h2><p>At PetWear, we take your privacy seriously. This policy outlines how we collect, use, and protect your personal information.</p><h3>Information We Collect</h3><p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us for support.</p><h3>How We Use Your Information</h3><p>We use the information to process orders, improve our services, and communicate with you about products and promotions.</p>', 'is_active' => true]);
        Page::create(['title' => 'Terms and Conditions', 'slug' => 'terms-and-conditions', 'content' => '<h2>Terms and Conditions</h2><p>By using PetWear, you agree to these terms and conditions. Please read them carefully.</p><h3>Orders & Payments</h3><p>All orders are subject to acceptance and availability. Prices are in Indian Rupees (₹) and include applicable taxes.</p><h3>Returns & Refunds</h3><p>We accept returns within 7 days of delivery. Items must be unused and in original packaging.</p>', 'is_active' => true]);

        // ===== COUPONS =====
        Coupon::create(['code' => 'PETWEAR10', 'type' => 'percent', 'value' => 10, 'min_order' => 500, 'max_uses' => 100, 'is_active' => true, 'expires_at' => now()->addMonths(6)]);
        Coupon::create(['code' => 'WELCOME15', 'type' => 'percent', 'value' => 15, 'min_order' => 1000, 'max_uses' => 50, 'is_active' => true, 'expires_at' => now()->addMonths(3)]);
        Coupon::create(['code' => 'FLAT200', 'type' => 'fixed', 'value' => 200, 'min_order' => 1500, 'max_uses' => 30, 'is_active' => true, 'expires_at' => now()->addMonth()]);

        // ===== REVIEWS =====
        $allProducts = Product::all();
        $reviewComments = [
            'My dog loves this! The fabric is so soft and comfortable. Highly recommended!',
            'Perfect fit and great quality. My cat looks adorable in this outfit.',
            'Amazing product! The stitching is very neat and the material is premium.',
            'My pet refuses to take it off! That says everything about the comfort level.',
            'Great value for money. Will definitely order more from PetWear.',
            'Beautiful design and excellent quality. My fur baby looks like a model!',
            'Ordered for Diwali and it was a hit! Everyone loved my pet\'s outfit.',
            'The color is exactly as shown. Very happy with the purchase.',
            'Super cute! My puppy gets so many compliments when wearing this.',
            'Comfortable and stylish. Perfect for daily walks and outings.',
        ];

        foreach ($allProducts->take(15) as $product) {
            $reviewCount = rand(1, 3);
            for ($r = 0; $r < $reviewCount; $r++) {
                Review::create([
                    'user_id' => $customers[array_rand($customers)]->id,
                    'product_id' => $product->id,
                    'rating' => rand(3, 5),
                    'comment' => $reviewComments[array_rand($reviewComments)],
                    'is_approved' => rand(0, 4) > 0,
                ]);
            }
        }
    }
}
