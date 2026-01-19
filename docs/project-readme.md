# Internal Procurement Platform (TallStackUI Starter)

## Docs index
- **Overview (bio + design system)**: `docs/my-sap-admin-panel.md`
- Workflow events: `docs/workflow-events-system.md`
- SAP/表示 comparisons & gaps: `docs/sap-ariba-comparison.md`, `docs/sap-ariba-gap-analysis.md`
- Upgrade planning: `docs/sap-upgrade-plan.md`, `docs/sap-upgrade-tracker.md`

## Purpose
This document captures the current system scope, how it maps to the SAP Ariba-style technical brief ("Texniki Tapşırıq"), and the remaining work required to achieve feature parity.

## Stack Snapshot
- **Framework**: Laravel 12, PHP 8.1+, Livewire 3 (see `composer.json`, `app/Livewire/*`).
- **UI Kit**: TallStackUI with custom OKLCH dark theme (documented in `docs/tallstackui-dark-system-integration.md`).
- **Key Domains**: Users, Markets, Products, Orders, Order Items, RFQs, Audit Logs (models under `app/Models`).
- **Auth & Routing**: Breeze-style auth scaffolding plus Livewire-driven dashboards (`routes/web.php`).

## Roles Implemented vs Brief
| Role | Spec Expectation | Current Support | Notes |
|------|------------------|-----------------|-------|
| Vendor Rep (Seller) | Register customer RFQs | Partial: sellers own markets (`app/Models/Market.php`, user flags); RFQs are registered by buyers via RFQ module, sellers still lack a dedicated RFQ view | Seller RFQ UX can be added later without changing existing seller flows |
| Procurement Officer (Buyer) | Route RFQs, collect quotes | **Foundational**: buyers can register internal RFQ requests and manage them via `app/Livewire/Rfqs/*`; routing to suppliers/quotes still missing | Next: invitations, quote capture, comparison, award |
| Suppliers | Submit quotes | Missing: only product catalogs exist; no supplier portal/forms | Need quote capture + optional supplier UI |
| Admin | Manage users, catalogs, audits, reports | Partial: user/product/market CRUD exists; audit logs exist | Requires RFQ dashboards, KPIs, exports, policy controls |

## Requirement Coverage Matrix
| Brief Section | Requirement | Status | Related Assets |
|---------------|-------------|--------|----------------|
| 3.1 RFQ Registration | Capture customer, product type, qty; unique ID; statuses (New, Routed, Awaiting Quotes, Completed, Cancelled) | **Foundational / Partially Implemented** | `app/Models/Request*`, `database/migrations/*requests*`, `app/Enums/RequestStatus.php`, `app/Services/RfqService.php`, `app/Livewire/Rfqs/*`, `resources/views/livewire/rfqs/*` |
| 3.2 Routing to Buyers | Assign procurement officer; auto-send to >=3 suppliers via email/notification | **Not Implemented** | RFQ request has `assigned_to` field, but no invitation logic or notifications yet |
| 3.3 Quote Intake | Suppliers submit prices; comparison panel | **Not Implemented** | `Quote`/`QuoteItem` entities and factories exist, but no Livewire quote entry or comparison UI |
| 3.4 Completion | Select best quote, handle payment/delivery | **Partially Implemented** | Orders can be created/closed; RFQ-to-PO award flow still missing |
| 3.5 Admin Panel | User/role mgmt, vendor/customer/product cards, performance stats, reports, audit trail | **Partially Implemented** | Livewire modules for users/markets/products; `Log` model for audit; no KPIs or exports |
| 4 Integrations | SAP-ready API/CSV exports, email/system notifications, responsive UI | **Partial** | Responsive UI achieved; no SAP API/CSV exporters or notifications |
| 5 Interfaces | Vendor, Buyer, Supplier, Admin portals | **Partial** | Buyer/Admin UIs exist; vendor-specific dashboard limited to market ownership; supplier portal absent |
| 6 Security & Audit | SSL, backups, audit logs | **Partial** | `Log` model exists; no documented backup/SSL enforcement |
| 7 Timeline | Requirement finalization, UI, backend, testing | **Missing** | No project plan tracked in repo |

## Current Strengths
- Mature user schema with supplier master fields (`app/Models/User.php` stores roles, tax IDs, Ariba Network ID placeholders, payment terms, performance counters).
- RFQ backbone in place: `Request`/`RequestItem` models, status enum, `RfqService`, seeders, and Livewire RFQ index/create views provide internal RFQ registration.
- Multi-market order items already relate buyers, markets, and products, giving a foundation for RFQ/PO separation.
- Logging infrastructure ready via `app/Models/Log.php` and Livewire log components.
- TallStackUI-based dashboard ensures responsive UX.

## Gap Summary
1. **RFQ/Quotation Workflow & Collaboration**: RFQ entities exist, but supplier invitations, quote capture, comparison views, and decision history are still missing.
2. **Workflow Automation**: Status engine exists via `RequestStatus` and `RequestStatusChanged` event, but assignment, reminders, and enforcement of "minimum three suppliers" rule are not yet wired into UI/services.
3. **Supplier Collaboration**: No supplier-facing access or secure submission channel.
4. **Analytics & Reporting**: Missing KPIs (response time, success rate), comparative vendor pricing reports, and export (Excel/PDF) pipelines.
5. **Integrations**: No Ariba-compatible APIs/CSV exports, email/SMS hooks, or payment/delivery bridges.
6. **Security/Compliance**: SSL/backups not encoded; audit logs lack before/after diffs and user context for all events.

## Proposed Roadmap
1. **Data Modeling & RFQ Foundations Sprint**
   - DONE (Foundational): introduce `requests`, `request_items`, `supplier_invitations`, `quotes`, `quote_items` tables and `RequestStatus` enum; seed sample RFQs.
   - NEXT: add `workflow_events` table and observer layer for RFQ status changes.
2. **Workflow & RFQ UI Sprint**
   - Build out Livewire modules for RFQ **show/update**, supplier invitations, and quote comparison dashboards, keeping Orders untouched.
   - Implement procurement officer assignment and notifications (Mail + in-app) driven by RFQ workflow events.
3. **Supplier Portal Sprint**
   - Create supplier authentication slice, quote submission forms, document upload, and communication log.
4. **Admin & Reporting Sprint**
   - Extend dashboards with KPIs, performance charts, and export actions (CSV/PDF) using `maatwebsite/excel` + `laravel-dompdf`.
   - Build weekly/monthly automated reports.
5. **Integration & Compliance Sprint**
   - Deliver SAP-friendly CSV/API endpoints, audit trail enhancements, SSL/backup documentation, and monitoring hooks.

## Getting Started
```bash
cp .env.example .env
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
php artisan serve
```

## References
- SAP-style brief (customer-provided).
- Existing documentation: `docs/tallstackui-dark-system-integration.md`, `docs/user-market-relationship.md`.
- Key entry points: `routes/web.php`, `app/Livewire/**`, `app/Models/**`, `resources/views/livewire/**`.
