<?php

namespace Database\Seeders;

use App\Enums\RequestStatus;
use App\Models\Product;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Request;
use App\Models\RequestItem;
use App\Models\SupplierInvitation;
use App\Models\User;
use App\Models\WorkflowEvent;
use Illuminate\Database\Seeder;

class RfqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reuse existing seeded users and products from DatabaseSeeder
        $buyers = User::where('is_buyer', true)->get();
        $suppliers = User::where('is_supplier', true)->get();
        $products = Product::all();

        // IMPORTANT: when running as part of DatabaseSeeder we now have a
        // fixed user pool (50 suppliers, 40 buyers, 10 sellers). We must
        // NOT create any extra users here, otherwise the global user cap
        // is violated. Therefore, the fallback user creation is only
        // allowed when RFQ seeding is executed in isolation.
        if ($buyers->isEmpty() && ! app()->runningInConsole('db:seed --class=DatabaseSeeder')) {
            $buyers = User::factory()->buyer()->count(5)->create();
        }

        if ($suppliers->isEmpty() && ! app()->runningInConsole('db:seed --class=DatabaseSeeder')) {
            $suppliers = User::factory()->supplier()->count(10)->create();
        }

        if ($products->isEmpty()) {
            $products = Product::factory()->count(40)->create();
        }

        // Create RFQs in different states
        $this->createDraftRfqs($buyers, $products);
        $this->createOpenRfqs($buyers, $suppliers, $products);
        $this->createClosedRfqs($buyers, $suppliers, $products);
        $this->createAwardedRfqs($buyers, $suppliers, $products);
        $this->createCancelledRfqs($buyers, $suppliers, $products);
    }

    /**
     * Create RFQs in draft state
     */
    private function createDraftRfqs($buyers, $products): void
    {
        // Create 5 draft RFQs
        for ($i = 0; $i < 5; $i++) {
            $buyer = $buyers->random();
            $rfq = Request::factory()->draft()->create([
                'buyer_id' => $buyer->id,
            ]);

            // Add 1-3 items to each RFQ
            $itemCount = rand(1, 3);
            for ($j = 0; $j < $itemCount; $j++) {
                RequestItem::factory()->create([
                    'request_id' => $rfq->id,
                    'product_name' => $products->random()->name,
                ]);
            }

            //TODO check workflow events logic
            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => null,
                'to_state' => RequestStatus::DRAFT->value,
                'description' => 'RFQ created as draft',
                'occurred_at' => $rfq->created_at,
            ]);
        }
    }

    /**
     * Create RFQs in open state with supplier invitations
     */
    private function createOpenRfqs($buyers, $suppliers, $products): void
    {
        // Create 8 open RFQs
        for ($i = 0; $i < 8; $i++) {
            $buyer = $buyers->random();
            $rfq = Request::factory()->open()->create([
                'buyer_id' => $buyer->id,
            ]);

            // Add 2-5 items to each RFQ
            $itemCount = rand(2, 5);
            $requestItems = [];
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $item = RequestItem::factory()->create([
                    'request_id' => $rfq->id,
                    'product_name' => $product->name,
                ]);
                $requestItems[] = $item;
            }

            // Invite 3-5 suppliers
            $invitedSuppliers = $this->getRandomItems($suppliers, rand(3, 5));
            foreach ($invitedSuppliers as $supplier) {
                SupplierInvitation::factory()->create([
                    'request_id' => $rfq->id,
                    'supplier_id' => $supplier->id,
                    'sent_at' => $rfq->created_at->addHours(rand(1, 24)),
                ]);
            }

            // Some suppliers have submitted quotes
            $quotingSuppliers = $this->getRandomItems($invitedSuppliers, rand(0, count($invitedSuppliers) - 1));
            foreach ($quotingSuppliers as $supplier) {
                $quote = Quote::factory()->create([
                    'request_id' => $rfq->id,
                    'supplier_id' => $supplier->id,
                ]);

                // Create quote items for each request item
                foreach ($requestItems as $requestItem) {
                    $unitPrice = rand(10, 1000);
                    $quantity = $requestItem->quantity;

                    QuoteItem::factory()->create([
                        'quote_id' => $quote->id,
                        'request_item_id' => $requestItem->id,
                        'description' => $requestItem->product_name ?? 'Product Item',
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'tax_rate' => rand(0, 20),
                    ]);
                }

                // Update supplier invitation
                SupplierInvitation::where('request_id', $rfq->id)
                    ->where('supplier_id', $supplier->id)
                    ->update([
                        'status' => 'accepted',
                        'responded_at' => now()->subDays(rand(1, 5)),
                    ]);
            }

            // Add workflow events
            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => RequestStatus::DRAFT->value,
                'to_state' => RequestStatus::OPEN->value,
                'description' => 'RFQ opened for quotes',
                'occurred_at' => $rfq->created_at->addHours(rand(1, 48)),
            ]);
        }
    }

    /**
     * Create RFQs in closed state with quotes
     */
    private function createClosedRfqs($buyers, $suppliers, $products): void
    {
        // Create 6 closed RFQs
        for ($i = 0; $i < 6; $i++) {
            $buyer = $buyers->random();
            $rfq = Request::factory()->closed()->create([
                'buyer_id' => $buyer->id,
                'created_at' => now()->subDays(rand(10, 30)),
            ]);

            // Add 2-5 items to each RFQ
            $itemCount = rand(2, 5);
            $requestItems = [];
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $item = RequestItem::factory()->create([
                    'request_id' => $rfq->id,
                    'product_name' => $product->name,
                ]);
                $requestItems[] = $item;
            }

            // Invite 3-5 suppliers (all have responded)
            $invitedSuppliers = $this->getRandomItems($suppliers, rand(3, 5));
            foreach ($invitedSuppliers as $supplier) {
                SupplierInvitation::factory()->create([
                    'request_id' => $rfq->id,
                    'supplier_id' => $supplier->id,
                    'sent_at' => $rfq->created_at->addHours(rand(1, 24)),
                    'status' => 'accepted',
                    'responded_at' => $rfq->created_at->addDays(rand(1, 3)),
                ]);

                // Create quote for each supplier
                $quote = Quote::factory()->create([
                    'request_id' => $rfq->id,
                    'supplier_id' => $supplier->id,
                ]);

                // Create quote items for each request item
                foreach ($requestItems as $requestItem) {
                    $unitPrice = rand(10, 1000);
                    $quantity = $requestItem->quantity;

                    QuoteItem::factory()->create([
                        'quote_id' => $quote->id,
                        'request_item_id' => $requestItem->id,
                        'description' => $requestItem->product_name ?? 'Product Item',
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'tax_rate' => rand(0, 20),
                    ]);
                }
            }

            // Add workflow events
            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => null,
                'to_state' => RequestStatus::DRAFT->value,
                'description' => 'RFQ created as draft',
                'occurred_at' => $rfq->created_at,
            ]);

            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => RequestStatus::DRAFT->value,
                'to_state' => RequestStatus::OPEN->value,
                'description' => 'RFQ opened for quotes',
                'occurred_at' => $rfq->created_at->addHours(rand(1, 48)),
            ]);

            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => RequestStatus::OPEN->value,
                'to_state' => RequestStatus::CLOSED->value,
                'description' => 'RFQ closed for evaluation',
                'occurred_at' => $rfq->created_at->addDays(rand(5, 10)),
            ]);
        }
    }

    /**
     * Create RFQs in awarded state
     */
    private function createAwardedRfqs($buyers, $suppliers, $products): void
    {
        // Create 4 awarded RFQs
        for ($i = 0; $i < 4; $i++) {
            $buyer = $buyers->random();
            $rfq = Request::factory()->awarded()->create([
                'buyer_id' => $buyer->id,
                'created_at' => now()->subDays(rand(20, 60)),
            ]);

            // Add 2-5 items to each RFQ
            $itemCount = rand(2, 5);
            $requestItems = [];
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $item = RequestItem::factory()->create([
                    'request_id' => $rfq->id,
                    'product_name' => $product->name,
                ]);
                $requestItems[] = $item;
            }

            // Invite 3-5 suppliers (all have responded)
            $invitedSuppliers = $this->getRandomItems($suppliers, rand(3, 5));
            $winningSupplier = $invitedSuppliers->random();

            foreach ($invitedSuppliers as $supplier) {
                $isWinner = $supplier->id === $winningSupplier->id;

                SupplierInvitation::factory()->create([
                    'request_id' => $rfq->id,
                    'supplier_id' => $supplier->id,
                    'sent_at' => $rfq->created_at->addHours(rand(1, 24)),
                    'status' => 'accepted',
                    'responded_at' => $rfq->created_at->addDays(rand(1, 3)),
                ]);

                // Create quote for each supplier
                $quote = Quote::factory()->create([
                    'request_id' => $rfq->id,
                    'supplier_id' => $supplier->id,
                    'status' => $isWinner ? 'accepted' : 'rejected',
                ]);

                // Create quote items for each request item
                foreach ($requestItems as $requestItem) {
                    // Make the winning supplier's price slightly better
                    $unitPrice = $isWinner
                        ? rand(10, 800)
                        : rand(100, 1000);
                    $quantity = $requestItem->quantity;

                    QuoteItem::factory()->create([
                        'quote_id' => $quote->id,
                        'request_item_id' => $requestItem->id,
                        'description' => $requestItem->product_name ?? 'Product Item',
                        'unit_price' => $unitPrice,
                        'quantity' => $quantity,
                        'tax_rate' => rand(0, 20),
                    ]);
                }
            }

            // Add workflow events
            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => null,
                'to_state' => RequestStatus::DRAFT->value,
                'description' => 'RFQ created as draft',
                'occurred_at' => $rfq->created_at,
            ]);

            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => RequestStatus::DRAFT->value,
                'to_state' => RequestStatus::OPEN->value,
                'description' => 'RFQ opened for quotes',
                'occurred_at' => $rfq->created_at->addHours(rand(1, 48)),
            ]);

            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => RequestStatus::OPEN->value,
                'to_state' => RequestStatus::CLOSED->value,
                'description' => 'RFQ closed for evaluation',
                'occurred_at' => $rfq->created_at->addDays(rand(5, 10)),
            ]);

            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => RequestStatus::CLOSED->value,
                'to_state' => RequestStatus::AWARDED->value,
                'description' => "RFQ awarded to supplier #{$winningSupplier->id}",
                'occurred_at' => $rfq->created_at->addDays(rand(12, 20)),
                'metadata' => [
                    'winning_supplier_id' => $winningSupplier->id,
                    'winning_quote_id' => Quote::where('request_id', $rfq->id)
                        ->where('supplier_id', $winningSupplier->id)
                        ->first()
                        ->id,
                ],
            ]);
        }
    }

    /**
     * Create RFQs in cancelled state
     */
    private function createCancelledRfqs($buyers, $suppliers, $products): void
    {
        // Create 3 cancelled RFQs
        for ($i = 0; $i < 3; $i++) {
            $buyer = $buyers->random();
            $rfq = Request::factory()->cancelled()->create([
                'buyer_id' => $buyer->id,
            ]);

            // Add 1-3 items to each RFQ
            $itemCount = rand(1, 3);
            for ($j = 0; $j < $itemCount; $j++) {
                RequestItem::factory()->create([
                    'request_id' => $rfq->id,
                    'product_name' => $products->random()->name,
                ]);
            }

            // 50% chance of having invited suppliers
            if (rand(0, 1) === 1) {
                // Invite 1-3 suppliers
                $invitedSuppliers = $this->getRandomItems($suppliers, rand(1, 3));
                foreach ($invitedSuppliers as $supplier) {
                    SupplierInvitation::factory()->create([
                        'request_id' => $rfq->id,
                        'supplier_id' => $supplier->id,
                        'sent_at' => $rfq->created_at->addHours(rand(1, 24)),
                    ]);
                }
            }

            // Add workflow events
            WorkflowEvent::factory()->create([
                'eventable_type' => Request::class,
                'eventable_id' => $rfq->id,
                'user_id' => $buyer->id,
                'event_type' => 'status_changed',
                'from_state' => null,
                'to_state' => RequestStatus::DRAFT->value,
                'description' => 'RFQ created as draft',
                'occurred_at' => $rfq->created_at,
            ]);

            // 50% chance of having been opened before cancellation
            if (rand(0, 1) === 1) {
                WorkflowEvent::factory()->create([
                    'eventable_type' => Request::class,
                    'eventable_id' => $rfq->id,
                    'user_id' => $buyer->id,
                    'event_type' => 'status_changed',
                    'from_state' => RequestStatus::DRAFT->value,
                    'to_state' => RequestStatus::OPEN->value,
                    'description' => 'RFQ opened for quotes',
                    'occurred_at' => $rfq->created_at->addHours(rand(1, 48)),
                ]);

                WorkflowEvent::factory()->create([
                    'eventable_type' => Request::class,
                    'eventable_id' => $rfq->id,
                    'user_id' => $buyer->id,
                    'event_type' => 'status_changed',
                    'from_state' => RequestStatus::OPEN->value,
                    'to_state' => RequestStatus::CANCELLED->value,
                    'description' => 'RFQ cancelled',
                    'occurred_at' => $rfq->created_at->addDays(rand(1, 5)),
                    'metadata' => [
                        'reason' => $this->getCancellationReason(),
                    ],
                ]);
            } else {
                WorkflowEvent::factory()->create([
                    'eventable_type' => Request::class,
                    'eventable_id' => $rfq->id,
                    'user_id' => $buyer->id,
                    'event_type' => 'status_changed',
                    'from_state' => RequestStatus::DRAFT->value,
                    'to_state' => RequestStatus::CANCELLED->value,
                    'description' => 'RFQ cancelled',
                    'occurred_at' => $rfq->created_at->addHours(rand(1, 72)),
                    'metadata' => [
                        'reason' => $this->getCancellationReason(),
                    ],
                ]);
            }
        }
    }

    /**
     * Get a random cancellation reason
     */
    private function getCancellationReason(): string
    {
        $reasons = [
            'Requirements changed',
            'Budget constraints',
            'Project cancelled',
            'Duplicate RFQ',
            'Insufficient supplier responses',
            'Internal reorganization',
            'Postponed to next quarter',
        ];

        return $reasons[array_rand($reasons)];
    }

    /**
     * Get random items from a collection, clamping the requested count to the collection size
     */
    protected function getRandomItems($collection, int $requested)
    {
        $count = $collection->count();

        if ($count === 0) {
            return $collection; // empty
        }

        // If requested is greater than available, just return all items in random order
        if ($requested >= $count) {
            return $collection->shuffle();
        }

        return $collection->random($requested);
    }
}
