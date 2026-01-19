# SAP Upgrade Task Tracker

This tracker mirrors the workstreams defined in `docs/sap-upgrade-plan.md`. Update the status and notes columns as each task progresses.

| Task ID | Workstream | Description | Key Artifacts / Target Paths | Status          | Owner | Notes / Next Action |
|---------|------------|-------------|------------------------------|-----------------|-------|-------------------|
| T1 | Foundations | Design RFQ data model (`requests`, `request_items`, `supplier_invitations`, `quotes`, `quote_items`, `workflow_events`), add enums, and stub services (`app/Services/RfqService.php`, `QuoteComparisonService.php`). | `database/migrations/*_create_requests_tables.php`, `app/Enums/RequestStatus.php`, `app/Services/` | **Completed**   | — | Created migrations, RequestStatus enum, and stub services |
| T2 | Foundations | Seed sample RFQ data + factories to support development and testing. | `database/factories/*`, `database/seeders/` | **Completed**   | — | Created factories for all RFQ-related models and implemented RfqSeeder |
| T3 | Workflow | Build Livewire RFQ components (`app/Livewire/Rfqs/*`) and Blade views (`resources/views/livewire/rfqs/*`) for intake, routing, and SLA tracking. | New component + view directories | **Completed**   | — | Components and views implemented under `app/Livewire/Rfq/*` and `resources/views/livewire/rfq/*` |
| T4 | Workflow | Implement workflow events, SLA jobs, and notifications (Mail/in-app) triggered from RFQ lifecycle. | `app/Jobs/`, `app/Notifications/`, `app/Listeners/` | **Completed** | — | Created workflow events (RequestStatusChanged, SupplierInvited, QuoteSubmitted, SlaReminderDue), listeners, notifications, CheckRfqDeadlines job, and registered all event listeners in AppServiceProvider |
| T5 | Supplier Portal | Create supplier routes (`routes/supplier.php`), Livewire components (`app/Livewire/Suppliers/*`), and views for quote submission, messaging, and attachments. | New routes file + components/views | **Completed** | — | Created supplier routes, middleware, dashboard, invitations, quotes components and views. Created SupplierInvitation, QuoteItem, and WorkflowEvent models |
| T6 | Analytics | Add metrics pipeline job (`app/Jobs/ComputeProcurementMetrics.php`), summary tables, and expose TallStackUI KPI widgets (Blade includes). | `database/migrations/*_metrics_tables.php`, `resources/views/components/kpi/*.blade.php` | Not Started     | — | |
| T7 | Reporting | Implement Excel/PDF export controllers (`app/Http/Controllers/Reports/*`) and Artisan scheduling for weekly/monthly reports. | Controllers, routes, `app/Exports/`, `app/Pdf/` | Not Started     | — | |
| T8 | Integrations | Build SAP export service + Artisan command (`SapExportService`, `ExportSapFeed`), plus API endpoints in `routes/api.php`. | `app/Services/SapExportService.php`, `app/Console/Commands/ExportSapFeed.php`, `app/Http/Controllers/Api/Sap/` | Not Started     | — | Define CSV/cXML specs with SAP team. |
| T9 | Governance | Add role config (`config/roles.php`), policies (`RequestPolicy`, `QuotePolicy`, `SupplierPortalPolicy`), and middleware for new routes. | `config/roles.php`, `app/Policies/*`, `app/Http/Middleware/*` | **-** | — | |
| T10 | Compliance | Extend audit logging (observers capturing before/after), document SSL/backups in `docs/security-playbook.md`, and script backup commands. | `app/Observers/*`, `docs/security-playbook.md`, `artisan` commands | **-**           | — | |

> Update this table after each significant milestone to maintain visibility into SAP alignment progress.
