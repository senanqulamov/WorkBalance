<?php

use App\Livewire\Monitoring\Rfq\WorkflowEvents;
use App\Models\Request;
use App\Models\User;
use App\Models\WorkflowEvent;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Create an admin user for testing
    $this->admin = User::factory()->create([
        'role' => 'admin',
        'is_buyer' => true,
    ]);

    // Create a buyer for RFQ ownership
    $this->buyer = User::factory()->create([
        'is_buyer' => true,
    ]);

    // Create a test RFQ
    $this->rfq = Request::factory()->create([
        'buyer_id' => $this->buyer->id,
        'title' => 'Test RFQ for Workflow Events',
        'status' => 'open',
    ]);

    // Create some workflow events
    WorkflowEvent::factory()->create([
        'eventable_type' => Request::class,
        'eventable_id' => $this->rfq->id,
        'user_id' => $this->buyer->id,
        'event_type' => 'status_changed',
        'from_state' => 'draft',
        'to_state' => 'open',
        'description' => 'RFQ opened for quotes',
        'occurred_at' => now()->subHours(2),
    ]);

    WorkflowEvent::factory()->create([
        'eventable_type' => Request::class,
        'eventable_id' => $this->rfq->id,
        'user_id' => $this->buyer->id,
        'event_type' => 'supplier_invited',
        'description' => 'Supplier invited to RFQ',
        'occurred_at' => now()->subHours(1),
        'metadata' => [
            'supplier_id' => 123,
            'supplier_name' => 'Test Supplier',
        ],
    ]);

    WorkflowEvent::factory()->create([
        'eventable_type' => Request::class,
        'eventable_id' => $this->rfq->id,
        'user_id' => $this->buyer->id,
        'event_type' => 'quote_submitted',
        'description' => 'Quote submitted by supplier',
        'occurred_at' => now()->subMinutes(30),
        'metadata' => [
            'quote_id' => 456,
            'quote_total' => 50000,
        ],
    ]);
});

test('workflow events component can be mounted', function () {
    actingAs($this->admin);

    Livewire::test(WorkflowEvents::class)
        ->assertStatus(200)
        ->assertSet('showModal', false)
        ->assertSet('requestId', null);
});

test('workflow events modal can be opened with RFQ ID', function () {
    actingAs($this->admin);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->assertSet('showModal', true)
        ->assertSet('requestId', $this->rfq->id)
        ->assertSee($this->rfq->title);
});

test('workflow events are displayed in timeline', function () {
    actingAs($this->admin);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->assertSee('RFQ opened for quotes')
        ->assertSee('Supplier invited to RFQ')
        ->assertSee('Quote submitted by supplier');
});

test('workflow events can be filtered by event type', function () {
    actingAs($this->admin);

    $component = Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->set('filterEventTypes', ['status_changed']);

    // Should see status_changed events
    $component->assertSee('RFQ opened for quotes');

    // Check that we have fewer events (should be filtered)
    expect($component->instance()->workflowEvents->count())->toBe(1);
});

test('workflow events can be filtered by user', function () {
    actingAs($this->admin);

    // Create an event with a different user
    $anotherUser = User::factory()->create(['name' => 'Another User']);
    WorkflowEvent::factory()->create([
        'eventable_type' => Request::class,
        'eventable_id' => $this->rfq->id,
        'user_id' => $anotherUser->id,
        'event_type' => 'comment_added',
        'description' => 'Comment added by another user',
        'occurred_at' => now(),
    ]);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->set('filterUser', 'Another User')
        ->assertSee('Comment added by another user')
        ->assertDontSee('RFQ opened for quotes');
});

test('workflow events can be filtered by date range', function () {
    actingAs($this->admin);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->set('filterDateFrom', now()->format('Y-m-d'))
        ->set('filterDateTo', now()->format('Y-m-d'))
        ->assertSee('Quote submitted by supplier')
        ->assertSee('Supplier invited to RFQ');
});

test('workflow events filters can be reset', function () {
    actingAs($this->admin);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->set('filterEventTypes', ['status_changed'])
        ->set('filterUser', 'Test')
        ->call('resetFilters')
        ->assertSet('filterEventTypes', [])
        ->assertSet('filterUser', null)
        ->assertSet('filterDateFrom', null)
        ->assertSet('filterDateTo', null);
});

test('workflow events modal can be closed', function () {
    actingAs($this->admin);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->assertSet('showModal', true)
        ->call('closeModal')
        ->assertSet('showModal', false)
        ->assertSet('requestId', null);
});

test('workflow events display correct event icons and colors', function () {
    actingAs($this->admin);

    $component = new \App\Livewire\Monitoring\Rfq\WorkflowEvents();

    expect($component->getEventIcon('status_changed'))->toBe('arrow-path');
    expect($component->getEventIcon('supplier_invited'))->toBe('user-plus');
    expect($component->getEventIcon('quote_submitted'))->toBe('document-check');
    expect($component->getEventIcon('sla_reminder'))->toBe('bell-alert');

    expect($component->getEventColor('status_changed'))->toBe('blue');
    expect($component->getEventColor('supplier_invited'))->toBe('green');
    expect($component->getEventColor('quote_submitted'))->toBe('purple');
    expect($component->getEventColor('sla_reminder'))->toBe('amber');
});

test('workflow events metadata is displayed', function () {
    actingAs($this->admin);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->assertSee('Test Supplier')
        ->assertSee('50000');
});

test('workflow events are ordered by occurrence date descending', function () {
    actingAs($this->admin);

    $events = WorkflowEvent::where('eventable_type', Request::class)
        ->where('eventable_id', $this->rfq->id)
        ->orderBy('occurred_at', 'desc')
        ->get();

    expect($events->first()->event_type)->toBe('quote_submitted');
    expect($events->last()->event_type)->toBe('status_changed');
});

test('workflow events show empty state when no events exist', function () {
    actingAs($this->admin);

    // Create a new RFQ without events
    $emptyRfq = Request::factory()->create([
        'buyer_id' => $this->buyer->id,
        'title' => 'Empty RFQ',
    ]);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $emptyRfq->id)
        ->assertSee('No workflow events');
});

test('workflow events pagination works correctly', function () {
    actingAs($this->admin);

    // Create additional events to exceed one page
    for ($i = 0; $i < 15; $i++) {
        WorkflowEvent::factory()->create([
            'eventable_type' => Request::class,
            'eventable_id' => $this->rfq->id,
            'user_id' => $this->buyer->id,
            'event_type' => 'comment_added',
            'description' => "Comment $i",
            'occurred_at' => now()->subMinutes($i),
        ]);
    }

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->assertSee('Comment 0')
        ->assertSet('quantity', 10);
});

test('workflow events display user information correctly', function () {
    actingAs($this->admin);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->assertSee($this->buyer->name);
});

test('workflow events show state transitions', function () {
    actingAs($this->admin);

    Livewire::test(WorkflowEvents::class)
        ->dispatch('monitoring::load::workflow_events', rfq: $this->rfq->id)
        ->assertSee('Draft')
        ->assertSee('Open');
});
