# SAP Ariba Upgrade Plan

This plan aligns the current TallStackUI-based procurement portal with SAP Ariba-style requirements described in `docs/project-readme.md` and the deeper comparison in `docs/sap-ariba-comparison.md`. The guiding principle is **preserve existing Livewire views/controllers** as-is; new behaviour should come from additive modules, services, or APIs unless a lightweight extension can wrap them without intrusive edits.

## Workstreams Overview
1. **RFQ & Sourcing Layer** – introduce the missing request/quote lifecycle while keeping legacy order screens intact. Foundations (tables, enums, `RfqService`, initial Livewire RFQ index/create) are already present; this workstream completes show/update, invitations, and workflow events.
2. **Supplier Collaboration Portal** – add a dedicated supplier UX without altering buyer/admin dashboards.
3. **Analytics, Reporting & Exports** – deliver Ariba-style KPIs and document exports via new services consumed by existing dashboards.
4. **Integrations & Notifications** – build SAP-ready CSV/API gateways plus email/system alerts separate from current controllers.
5. **Governance, Security & Compliance** – strengthen roles, audits, and backup/SSL documentation with additive middleware/config.

## 1. RFQ & Sourcing Layer
- **Existing Foundations**: RFQ schema (`requests`, `request_items`, `supplier_invitations`, `quotes`, `quote_items`), `App\Enums\RequestStatus`, `app/Services/RfqService.php`, factories/seeders, and base Livewire RFQ index/create components under `app/Livewire/Rfqs/*` and `resources/views/livewire/rfqs/*`.
- **New Data Structures**: add `workflow_events` table and a small `WorkflowEvent` model to record RFQ status transitions and SLA timestamps.
- **Domain Services**: extend `RfqService` and `QuoteComparisonService.php` to orchestrate lifecycle logic (status transitions, invitations, quote comparisons) while exposing read-only DTOs for existing order views.
- **Livewire Modules**: finish the RFQ slice under `app/Livewire/Rfqs/*` (Index, Create, **Show**, **Update/Edit**, basic **Invitations** and **Comparison** partials) with corresponding views in `resources/views/livewire/rfqs`. Existing `Orders` components stay untouched but can optionally display linked RFQ summaries via injected services.
- **Status Engine**: configure enum-style status objects (e.g., `App\Enums\RequestStatus`) and background jobs for SLA reminders, ensuring they update new tables only.

## 2. Supplier Collaboration Portal
- **Routing Slice**: add supplier-specific routes (e.g., `/supplier/dashboard`) pointing to new Livewire modules in `app/Livewire/Suppliers/*` without modifying buyer/admin pages.
- **Features**: quote submission forms, document uploads, messaging threads, and visibility into invitations—all backed by the new RFQ tables.
- **Access Control**: leverage new policies/guards that extend `User` capabilities, keeping existing authentication and middleware unchanged.

## 3. Analytics, Reporting & Exports
- **Metrics Pipelines**: create scheduled jobs (e.g., `app/Jobs/ComputeProcurementMetrics.php`) populating summary tables. Dashboards can consume these via new read-only APIs.
- **Reporting Controllers**: place Excel/PDF exporters under `app/Http/Controllers/Reports/*`, using packages like `maatwebsite/excel` and `laravel-dompdf`. Trigger them via new routes/buttons, leaving existing index screens unchanged if desired.
- **KPI Widgets**: expose TallStackUI-compatible components (cards/charts) that can be slotted into dashboards through Blade includes without rewriting original Livewire classes.

## 4. Integrations & Notifications
- **SAP Export Service**: add `app/Services/SapExportService.php` to generate CSV/cXML payloads for RFQs, POs, and quotes. Provide CLI/artisan commands (`app/Console/Commands/ExportSapFeed.php`) for scheduled drops.
- **API Layer**: design `routes/api.php` endpoints for SAP ingestion, wrapping new services so legacy controllers remain unaffected.
- **Notifications**: build event-driven notifications (Mail, database, optional SMS) triggered from the new RFQ workflow events via Laravel listeners; this augments behaviour without editing existing forms.

## 5. Governance, Security & Compliance
- **RBAC & Policies**: introduce `RequestPolicy`, `QuotePolicy`, and `SupplierPortalPolicy` plus role constants in `config/roles.php`. Apply middleware stacks on new routes to avoid touching existing ones.
- **Audit Enhancements**: extend `app/Models/Log.php` via observers/listeners that capture before/after snapshots for new entities only; current logging logic stays intact.
- **Operational Docs**: add `docs/security-playbook.md` describing SSL enforcement, backup cadence, and monitoring. Implement artisan commands or scripts for backups without altering controllers/views.

## Execution Phasing
1. **Foundations Sprint**: (Done) schema migrations, enums, and core RFQ services + initial index/create components; (Next) `workflow_events` and basic invitation helpers.
2. **Workflow Sprint**: implement RFQ show/edit components, SLA jobs, and invitation/quote flows; integrate notifications.
3. **Collaboration Sprint**: finish supplier portal UI, messaging, and attachment handling; connect to RFQ services.
4. **Analytics & Reporting Sprint**: deliver metrics jobs, KPIs, export endpoints, and optional dashboard widgets.
5. **Integration & Compliance Sprint**: build SAP export commands/APIs, finalize documentation, and roll out RBAC/audit upgrades.

## Guiding Principles
- **Additive Development**: prefer new modules/services over edits to `app/Livewire/Orders/*`, `Markets/*`, `Users/*`, etc.
- **Compatibility Layers**: when legacy components need new data, inject services or view composers rather than rewriting component logic.
- **Documentation First**: update `docs/` alongside each sprint to track parity progress and operating procedures.

Use this plan as the north star for backlog grooming and sprint scoping while preserving existing UI/logic.
