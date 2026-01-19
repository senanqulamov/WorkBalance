# Workflow Events System Documentation

## Overview

The **Workflow Events System** is a comprehensive event tracking and audit trail mechanism designed to record and monitor all significant activities, state changes, and interactions within the dPanel procurement platform. This system provides complete transparency and traceability for Request for Quotation (RFQ) workflows, supplier interactions, and quote management processes.

## Purpose

The Workflow Events system serves multiple critical purposes:

1. **Audit Trail**: Maintains a complete, immutable record of all actions and state transitions
2. **Compliance & Governance**: Supports regulatory requirements and internal compliance policies
3. **Process Analytics**: Enables analysis of workflow patterns, bottlenecks, and efficiency metrics
4. **Transparency**: Provides stakeholders with visibility into process progress and decision-making
5. **Debugging & Support**: Facilitates troubleshooting and customer support by providing detailed activity logs
6. **SLA Monitoring**: Tracks deadlines, reminders, and time-sensitive activities

## Database Structure

### Table: `workflow_events`

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `eventable_type` | string | Polymorphic type (e.g., Request, Quote) |
| `eventable_id` | bigint | Polymorphic ID of the parent entity |
| `user_id` | bigint | User who triggered the event (nullable) |
| `event_type` | string | Type of event (see Event Types below) |
| `from_state` | string | Previous state (for state transitions) |
| `to_state` | string | New state (for state transitions) |
| `description` | text | Human-readable description |
| `metadata` | json | Additional contextual data |
| `occurred_at` | timestamp | When the event occurred |
| `created_at` | timestamp | Record creation timestamp |
| `updated_at` | timestamp | Record update timestamp |

**Indexes:**
- `eventable_type` + `eventable_id` (polymorphic relationship)
- `event_type` (fast filtering by event type)
- `occurred_at` (chronological queries)
- `user_id` (user activity tracking)

## Event Types

### Core Event Types

1. **status_changed**
   - Triggered when an RFQ or Quote status changes
   - Captures: `from_state`, `to_state`, user who made the change
   - Example: Draft → Open, Open → Closed, Closed → Awarded

2. **supplier_invited**
   - Triggered when a supplier is invited to participate in an RFQ
   - Metadata: supplier details, invitation ID
   - Example: Buyer invites 5 suppliers to bid on RFQ #123

3. **quote_submitted**
   - Triggered when a supplier submits a quote
   - Metadata: quote ID, supplier details, total amount
   - Example: Supplier XYZ submits quote with total $50,000

4. **sla_reminder**
   - Triggered when SLA deadlines are approaching or breached
   - Metadata: deadline information, remaining time
   - Example: RFQ deadline in 3 days reminder sent

5. **assigned**
   - Triggered when an RFQ is assigned to a specific user or team
   - Metadata: assignee details
   - Example: RFQ #456 assigned to Procurement Manager

6. **comment_added**
   - Triggered when users add comments or notes
   - Metadata: comment content, visibility settings
   - Example: Buyer adds internal note about budget constraints

7. **document_uploaded**
   - Triggered when documents are attached to RFQs or quotes
   - Metadata: file name, file type, file size
   - Example: Technical specifications PDF uploaded to RFQ

8. **quote_accepted**
   - Triggered when a buyer accepts a quote
   - Metadata: quote details, acceptance criteria
   - Example: Quote from Supplier ABC accepted for $45,000

9. **quote_rejected**
   - Triggered when a buyer rejects a quote
   - Metadata: rejection reason
   - Example: Quote rejected due to pricing

## Model Relationships

### WorkflowEvent Model

```php
class WorkflowEvent extends Model
{
    // Polymorphic relationship to parent entity
    public function eventable(): MorphTo
    
    // User who triggered the event
    public function user(): BelongsTo
}
```

### Request Model

```php
class Request extends Model
{
    // Get all workflow events for this RFQ
    public function workflowEvents(): MorphMany
}
```

### Quote Model

```php
class Quote extends Model
{
    // Get all workflow events for this quote
    public function workflowEvents(): MorphMany
}
```

## Event Recording

### Automatic Event Recording

Events are automatically recorded through Laravel's event listener system:

**Event Listener: `RecordWorkflowEvent`**

Located at: `app/Listeners/RecordWorkflowEvent.php`

This listener handles multiple event types:
- `RequestStatusChanged`
- `SupplierInvited`
- `QuoteSubmitted`
- `SlaReminderDue`

The listener implements `ShouldQueue` for asynchronous processing to avoid blocking user requests.

### Manual Event Recording

You can manually record workflow events in your code:

```php
use App\Models\WorkflowEvent;

WorkflowEvent::create([
    'eventable_type' => Request::class,
    'eventable_id' => $rfq->id,
    'user_id' => auth()->id(),
    'event_type' => 'status_changed',
    'from_state' => 'draft',
    'to_state' => 'open',
    'description' => 'RFQ opened for quotes',
    'occurred_at' => now(),
    'metadata' => [
        'user_name' => auth()->user()->name,
        'additional_info' => 'Custom data here'
    ],
]);
```

## Usage Examples

### Retrieving Workflow Events

#### Get all events for an RFQ:
```php
$rfq = Request::find(1);
$events = $rfq->workflowEvents()
    ->orderBy('occurred_at', 'desc')
    ->get();
```

#### Get specific event types:
```php
$statusChanges = $rfq->workflowEvents()
    ->where('event_type', 'status_changed')
    ->get();
```

#### Get events with user information:
```php
$events = $rfq->workflowEvents()
    ->with('user')
    ->orderBy('occurred_at', 'desc')
    ->get();
```

#### Get events within a date range:
```php
$events = $rfq->workflowEvents()
    ->whereBetween('occurred_at', [
        now()->subDays(30),
        now()
    ])
    ->get();
```

### Querying Event Metadata

```php
// Find events with specific metadata
$awardedEvents = WorkflowEvent::where('event_type', 'status_changed')
    ->where('to_state', 'awarded')
    ->whereJsonContains('metadata->winning_supplier_id', $supplierId)
    ->get();
```

## Display & Visualization

### Timeline View

Workflow events are typically displayed as a timeline showing:
- Event icon and color coding by type
- Timestamp (relative and absolute)
- User who triggered the event
- Description and context
- State transitions (from → to)
- Additional metadata

### Event Type Styling

| Event Type | Icon | Color |
|------------|------|-------|
| status_changed | arrow-path | Blue |
| supplier_invited | user-plus | Green |
| quote_submitted | document-check | Purple |
| sla_reminder | bell-alert | Amber |
| assigned | user-circle | Indigo |
| comment_added | chat-bubble | Gray |
| document_uploaded | paper-clip | Slate |

## Monitoring & Analytics

### Key Metrics from Workflow Events

1. **Average Time Between States**
   - Calculate time from draft → open → closed → awarded
   - Identify bottlenecks in the procurement process

2. **Supplier Response Time**
   - Time from invitation to quote submission
   - Measure supplier engagement

3. **Decision Time**
   - Time from quote submission to acceptance/rejection
   - Optimize evaluation processes

4. **SLA Compliance**
   - Track on-time vs. late completions
   - Monitor SLA breach frequencies

### Example Analytics Queries

```php
// Average time from open to closed
$avgTimeToClose = WorkflowEvent::where('event_type', 'status_changed')
    ->where('to_state', 'closed')
    ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, 
        (SELECT occurred_at FROM workflow_events we2 
         WHERE we2.eventable_id = workflow_events.eventable_id 
         AND we2.to_state = "open"), 
        occurred_at)) as avg_hours')
    ->value('avg_hours');
```

## Best Practices

### 1. Always Set occurred_at
Use `occurred_at` instead of relying on `created_at` for accurate timeline representation.

### 2. Include Meaningful Descriptions
Write human-readable descriptions that explain what happened and why.

### 3. Use Metadata for Context
Store additional context in the metadata field for future analysis and debugging.

### 4. Queue Heavy Operations
Use queued listeners to avoid blocking user requests when recording events.

### 5. Index for Performance
Ensure proper database indexes for frequently queried fields.

### 6. Consistent Event Types
Use standardized event type names across the application.

### 7. Security & Privacy
- Don't store sensitive data in descriptions or metadata
- Consider data retention policies for compliance
- Implement proper access controls for viewing events

## Event Seeding

The system includes comprehensive seeding for development and testing:

**Seeder: `RfqSeeder`**

Creates workflow events for:
- Draft RFQs with initial creation events
- Open RFQs with status transitions and supplier invitations
- Closed RFQs with complete lifecycle events
- Awarded RFQs with winner selection events
- Cancelled RFQs with cancellation reasons

## Integration Points

### 1. Event Broadcasting
Workflow events can be broadcast in real-time using Laravel Echo:
```php
broadcast(new WorkflowEventOccurred($workflowEvent));
```

### 2. Notifications
Trigger notifications based on workflow events:
```php
event(new RequestStatusChanged($request, $oldStatus, $newStatus, $user));
```

### 3. Webhooks
External systems can be notified of workflow events via webhooks.

### 4. Reports & Exports
Generate reports and exports based on workflow event data.

## Future Enhancements

1. **Event Versioning**: Track changes to events themselves
2. **Event Rollback**: Ability to undo certain events
3. **Advanced Filtering**: Complex queries across multiple event types
4. **Real-time Dashboard**: Live updates of workflow events
5. **AI-Powered Insights**: Pattern recognition and predictive analytics
6. **Custom Event Types**: Allow users to define custom events
7. **Event Templates**: Predefined event configurations

## Troubleshooting

### Events Not Recording

1. Check listener registration in `EventServiceProvider`
2. Verify queue workers are running: `php artisan queue:work`
3. Check database connection and migrations
4. Verify event dispatching in controllers/services

### Performance Issues

1. Add database indexes on frequently queried columns
2. Implement pagination for large result sets
3. Use eager loading to avoid N+1 queries
4. Consider archiving old events to separate tables

### Data Integrity

1. Use database transactions when creating related records
2. Implement foreign key constraints
3. Regular backups of workflow_events table
4. Monitor for orphaned records

## Conclusion

The Workflow Events System provides a robust foundation for tracking, monitoring, and analyzing all activities within the dPanel procurement platform. By maintaining a comprehensive audit trail, it enables compliance, improves transparency, and supports data-driven decision-making throughout the procurement lifecycle.
