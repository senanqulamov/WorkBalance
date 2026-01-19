# SAP Ariba Alignment Brief

## 1. What SAP Ariba Delivers
SAP Ariba is SAP's cloud procurement suite that unifies sourcing, contract, supplier, and procure-to-pay processes on top of the Ariba Network. Core pillars:
- **Supplier Lifecycle & Risk**: supplier onboarding, qualification, performance scoring, compliance evidence, and integration with third-party risk feeds.
- **Strategic Sourcing**: RFIs/RFQs, auctions, collaborative scoring, approval workflows, and award scenarios with what-if analysis.
- **Procure-to-Pay (P2P)**: guided buying UI, catalogs, purchase requisitions/orders, service entry sheets, receiving, and three-way matching.
- **Commerce Automation**: cXML/EDI-based document exchange (PO, Advance Ship Notice, Invoice) plus integration kits for SAP S/4HANA and non-SAP ERPs.
- **Spend Analysis & Reporting**: real-time dashboards, KPI packs (cycle time, savings, supplier risk), and scheduled exports.
- **Compliance & Security**: granular permissions, audit logging, certifications (SOC, ISO), data residency controls, and disaster recovery SLAs.

## 2. Snapshot of Current Project (`dpanel`)
- **Tech Stack**: Laravel 12 + Livewire 3, TallStackUI theme (`README.md`, `routes/web.php`).
- **Domain Models**: Users with role flags and supplier metadata (`app/Models/User.php`), Markets, Products, Orders, OrderItems, RFQs (`app/Models/Request*`), Logs.
- **UI Modules**: Livewire dashboards for users/markets/products/orders/logs, plus RFQ index/create (`app/Livewire/**`).
- **Audit Trail**: `app/Models/Log.php` + Livewire log viewer.
- **Theming/UX**: custom OKLCH dark mode across TallStackUI (`docs/tallstackui-dark-system-integration.md`).

## 3. Capability Comparison
| SAP Ariba Pillar | Native Offerings | Current Project Status | Notes/Files |
|------------------|------------------|-----------------------|-------------|
| Supplier Lifecycle Mgmt | End-to-end onboarding, questionnaires, risk scoring, approval workflows | **Foundational**: user flags + supplier fields exist, but no workflows or risk scoring | `app/Models/User.php` contains supplier fields; no onboarding UI yet |
| RFQ/Sourcing | RFIs/RFQs, auctions, supplier communications, scenario analysis | **Foundational / Partial**: RFQ entities, status enum, and internal RFQ intake/listing exist; supplier invitations, quote capture, auctions, and messaging are still missing | `app/Models/Request*`, `app/Enums/RequestStatus.php`, `app/Services/RfqService.php`, `app/Livewire/Rfqs/*`, `resources/views/livewire/rfqs/*` |
| Guided Buying & Catalogs | Buyer-friendly UI, category policies, preferred supplier routing | **Partial**: products/markets exist; no policy engine or guided paths | `app/Livewire/Products/*`, `app/Livewire/Markets/*` |
| Procurement Workflow | Approvals, auto-routing, SLA tracking, collaboration tools | **Missing**: no approval chains, assignments, or SLA metrics | would require workflow engine/new tables |
| Commerce Automation | cXML/EDI, PO/Invoice automation, ASN, integration packs | **Missing**: system stores orders only; no external document exchange | need integration microservices/API |
| Spend Analytics | Real-time dashboards, benchmark KPIs, savings tracking | **Missing**: dashboards show raw lists only; no analytics or savings modeling | `app/Livewire/Dashboard/Index.php` (basic) |
| Reporting & Exports | Scheduled PDF/Excel, regulatory reports | **Missing**: no export controllers/packages configured | add `maatwebsite/excel`, `laravel-dompdf` |
| Security & Compliance | SOC/ISO controls, configurable roles, audit, DLP, backups | **Partial**: audit logs exist; no RBAC granularity, no documented backups, SSL enforcement unspecified | `app/Models/Log.php`, `config/*` |
| Supplier Collaboration | Supplier portal, messaging, dispute handling | **Missing**: suppliers have no login experience aside from being a `User` | requires dedicated routes + Livewire components |

## 4. Distance-from-Ariba Assessment
| Area | Gap Severity | Key Missing Elements |
|------|-------------|----------------------|
| RFQ & Sourcing Lifecycle | **High (Foundations in place)** | Supplier invitations, quote capture UI, comparison and award flows, auctions, and routing rules (e.g., min 3 suppliers) |
| Integration & Automation | High | SAP cXML/CSV exports, email/SMS notifications, API endpoints |
| Analytics & Reporting | High | KPIs (cycle time, savings), vendor comparison reports, scheduled exports |
| Supplier Experience | Medium | Portal UI, onboarding questionnaires, qualification workflows |
| Security & Compliance | Medium | RBAC policies, SSL/backup documentation, configurable audit policies |
| Catalog & Guided Buying | Medium | Policy enforcement, preferred vendor logic, dynamic forms |

## 5. Recommended Bridging Steps
1. **Model & Workflow Foundations**: (Partially done) RFQ tables, enums, and `RfqService` already exist; next, add `workflow_events` and status transition rules driven from RFQ UI.
2. **Supplier Collaboration Layer**: build supplier dashboard with secure quote submission, messaging, and attachment handling (leveraging existing `User` schema` and RFQ invitations).
3. **Integration Track**: deliver CSV/API exports aligned with SAP Ariba inbound formats; plan cXML connectors and notification services.
4. **Analytics & Reporting**: implement metrics aggregation jobs, dashboards with KPIs, and Excel/PDF export actions for vendor comparisons and SLA tracking.
5. **Governance & Compliance**: define role-based policies, document SSL/backups, enrich `Log` entries with before/after metadata, and add monitoring hooks.

## 6. How to Use This Doc
- Share with stakeholders to position the current Laravel stack as a prototype relative to SAP Ariba.
- Use the gap tables to prioritize backlog items (`docs/project-readme.md` already contains a sprint-oriented roadmap).
- Update after each sprint to reflect progress toward SAP-compatible procurement coverage.
