<?php

namespace Database\Seeders;

use App\Models\Log;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting comprehensive database seeding...');

        // STEP 1: Ensure core roles and permissions exist before any users are created
        $this->command->info('📋 Step 1: Creating roles and permissions...');
        $this->call(RolesAndPermissionsSeeder::class);

        // STEP 2: Create users with proper distribution
        // 50 suppliers, 40 buyers, 10 sellers (100 total + 1 admin)
        $this->command->info('👥 Step 2: Creating users...');

        // Create main admin (outside the 100-user pool)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'is_buyer' => true,
                'is_seller' => true,
                'is_supplier' => true,
                'role' => 'admin',
                'is_admin' => true,
            ]
        );

        if ($admin->wasRecentlyCreated || ! $admin->roles()->where('name', 'admin')->exists()) {
            $adminRole = \App\Models\Role::where('name', 'admin')->first();
            if ($adminRole && ! $admin->roles()->where('roles.id', $adminRole->id)->exists()) {
                $admin->roles()->attach($adminRole->id);
            }
        }

        // Create exactly 10 sellers
        $this->command->info('  → Creating 10 sellers...');
        $sellers = collect();
        for ($i = 1; $i <= 10; $i++) {
            $seller = User::factory()->seller()->create([
//                'name' => "Seller User {$i}",
                'email' => "seller_{$i}@example.com",
            ]);
            $sellers->push($seller);
        }
        $this->command->info("  ✓ Created {$sellers->count()} sellers");

        // Create exactly 50 suppliers
        $this->command->info('  → Creating 50 suppliers...');
        $suppliers = collect();
        for ($i = 1; $i <= 50; $i++) {
            $supplier = User::factory()->supplier()->create([
//                'name' => "Supplier User {$i}",
                'email' => "supplier_{$i}@example.com",
            ]);
            $suppliers->push($supplier);
        }
        $this->command->info("  ✓ Created {$suppliers->count()} suppliers");

        // Create exactly 40 buyers
        $this->command->info('  → Creating 40 buyers...');
        $buyers = collect();
        for ($i = 1; $i <= 40; $i++) {
            $buyer = User::factory()->buyer()->create([
//                'name' => "Buyer User {$i}",
                'email' => "buyer_{$i}@example.com",
            ]);
            $buyers->push($buyer);
        }
        $this->command->info("  ✓ Created {$buyers->count()} buyers");

        // STEP 3: Create markets - each seller gets exactly 10 markets
        $this->command->info('🏪 Step 3: Creating markets (10 per seller = 100 total)...');
        $markets = collect();
        foreach ($sellers as $index => $seller) {
            $sellerNumber = $index + 1;
            for ($m = 1; $m <= 10; $m++) {
                $market = Market::factory()->create([
                    'user_id' => $seller->id,
                ]);
                $markets->push($market);
            }
        }
        $this->command->info("  ✓ Created {$markets->count()} markets");

        // STEP 4: Create products - each market gets 5-30 products
        $this->command->info('📦 Step 4: Creating products (5-30 per market)...');
        $products = collect();
        $totalProducts = 0;
        foreach ($markets as $market) {
            $productCount = rand(5, 30);
            for ($p = 0; $p < $productCount; $p++) {
                $product = Product::factory()->create([
                    'market_id' => $market->id,
                    'supplier_id' => $suppliers->random()->id,
                ]);
                $products->push($product);
                $totalProducts++;
            }
        }
        $this->command->info("  ✓ Created {$totalProducts} products across {$markets->count()} markets");

        // STEP 5: Create orders for suppliers
        $this->command->info('🛒 Step 5: Creating orders for suppliers...');
        $orderCount = 0;
        foreach ($suppliers as $supplier) {
            // Each supplier gets 2-5 orders
            $numOrders = rand(2, 5);
            for ($o = 0; $o < $numOrders; $o++) {
                // Pick a random market (and thus a seller)
                $market = $markets->random();
                $seller = $market->seller;

                /** @var \App\Models\Order $order */
                $order = Order::create([
                    'user_id' => $supplier->id,
                    'seller_id' => $seller->id,
                    'total' => 0,
                    'status' => fake()->randomElement(['pending', 'accepted', 'processing', 'completed', 'cancelled']),
                ]);

                // Attach 1-4 products from this market
                $marketProducts = $products->where('market_id', $market->id);
                if ($marketProducts->isEmpty()) {
                    continue;
                }

                $lineProducts = $marketProducts->random(min(rand(1, 4), $marketProducts->count()));
                $total = 0;

                foreach ($lineProducts as $product) {
                    $qty = rand(1, 5);
                    $unit = (float) $product->price;
                    $subtotal = round($qty * $unit, 2);

                    $order->items()->create([
                        'product_id' => $product->id,
                        'market_id' => $product->market_id,
                        'quantity' => $qty,
                        'unit_price' => $unit,
                        'subtotal' => $subtotal,
                    ]);

                    $total += $subtotal;
                }

                $order->forceFill(['total' => $total])->saveQuietly();
                $orderCount++;
            }
        }
        $this->command->info("  ✓ Created {$orderCount} orders for buyers");

        // STEP 6: Create logs with proper user associations
        $this->command->info('📝 Step 6: Creating activity logs...');
        $allUsers = collect([$admin])->merge($sellers)->merge($suppliers)->merge($buyers);
        for ($i = 0; $i < 300; $i++) {
            Log::factory()->create([
                'user_id' => $allUsers->random()->id,
            ]);
        }
        $this->command->info('  ✓ Created 300 activity logs');

        // STEP 7: Create RFQs, Quotes, and supplier invitations
        $this->command->info('📋 Step 7: Creating RFQs, quotes, and supplier interactions...');
        $this->call(RfqSeeder::class);
        $this->command->info('  ✓ RFQ data seeded successfully!');

        // STEP 8: Validation and summary
        $this->command->info('✅ Step 8: Validating seeded data...');

        $nonAdminUsers = User::where('is_admin', false)->count();
        $marketCount = Market::count();
        $productCount = Product::count();
        $orderCountFinal = Order::count();
        $logCount = Log::count();

        $this->command->info("\n📊 Seeding Summary:");
        $this->command->info("  • Total Users: " . ($nonAdminUsers + 1) . " (1 admin + {$nonAdminUsers} regular users)");
        $this->command->info("    - Sellers: {$sellers->count()}");
        $this->command->info("    - Suppliers: {$suppliers->count()}");
        $this->command->info("    - Buyers: {$buyers->count()}");
        $this->command->info("  • Markets: {$marketCount}");
        $this->command->info("  • Products: {$productCount}");
        $this->command->info("  • Orders: {$orderCountFinal}");
        $this->command->info("  • Logs: {$logCount}");

        // Verify role assignments
        $usersWithoutRoles = User::doesntHave('roles')->count();
        if ($usersWithoutRoles > 0) {
            $this->command->error("⚠️  Warning: {$usersWithoutRoles} users without roles attached!");
        } else {
            $this->command->info("  • All users have proper role assignments ✓");
        }

        if ($nonAdminUsers !== 100) {
            $this->command->warn("⚠️  Expected 100 non-admin users, got {$nonAdminUsers}");
        }

        $this->command->info("\n🎉 Database seeding completed successfully!");
    }
}
