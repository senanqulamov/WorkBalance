# My SAP-Style Procurement Project — Admin Panel & Design System

> **Bio (short)**: A Laravel + Livewire admin panel inspired by SAP Ariba-style procurement workflows (RFQs → supplier invitations → quotes → award). Built on TallStackUI + Tailwind with a custom dark theme and a growing set of reusable Blade components.

## What this project is
This repository is an **internal procurement platform** that models a modern, SAP/Ariba-like workflow, with a responsive admin panel for buyers and admins.

It focuses on:
- **RFQ registration & tracking** (internal requests, items, statuses)
- **Supplier collaboration primitives** (invitations, quote intake, comparison — some in progress)
- **Auditability** (workflow/status events, logs)
- **A consistent UI system** (TallStackUI + Tailwind + custom components)

## Stack
- **Backend**: Laravel 12 (PHP 8.1+)
- **Frontend**: Livewire v3, Blade
- **Styling**: Tailwind CSS + TallStackUI
- **Build**: Vite
- **Quality**: PHPUnit/Pest, PHPStan, Pint

## Product scope (modules)
These modules exist today (some partial, some foundational):
- **Users & Roles**: admin, buyer/procurement, vendor/supplier fields
- **Markets & Product Catalogs**: multi-market data model
- **RFQs (Requests)**: request + request items + status enum
- **Orders & Order Items**: purchase/order backbone (separate from RFQ award flow)
- **Audit Logs / Workflow Events**: logging + event-driven workflow hooks

## UX principles (admin panel)
This admin panel is designed to feel “enterprise” but fast:
- **Dense but readable layouts**: tables, filters, status chips, quick actions
- **Clear status language**: statuses are the primary navigation (New → Routed → Awaiting Quotes → Completed)
- **Predictable component behavior**: consistent spacing/typography, same hover/focus patterns everywhere
- **Dark-first theme**: balanced contrast, low-glare backgrounds, clear semantic colors

---

# Design system

## Theme & tokens (guidelines)
While Tailwind provides utilities, the UI is treated like a system with repeatable rules:

### Color
- **Base surfaces**: slate/zinc dark surfaces for panels and tables
- **Borders**: subtle zinc borders (`border-zinc-700/50`) to avoid heavy boxes
- **Primary/Success**: emerald accents for positive state and selections
- **Text**: slate shades for readable hierarchy (primary text vs muted labels)

### Interaction
- **Hover**: small contrast lift (border + background)
- **Active**: subtle scale down (`active:scale-[0.98]`) for tactile feedback
- **Focus**: rely on accessible focus styles (don’t remove outlines without replacement)

### Spacing & sizing
- Prefer **8px rhythm** (Tailwind `2/4/6/8` style spacing) for padding/gaps.
- Controls use consistent heights and rounded corners.

### Accessibility
- Use semantic inputs (native `input`, `label`), and enhance visually.
- Keep hit targets comfortable (≥ 40px in height when used as pills/buttons).

---

# Component library (custom)

This project uses TallStackUI components and adds custom Blade components under `resources/views/components/`.

## `cst_checkbox` (pill checkbox)
A visually enhanced checkbox designed for filter pills and multi-select tags.

**File**: `resources/views/components/cst_checkbox.blade.php`

### Props
- `label` (string): text label
- `value` (mixed): checkbox value (useful for array models)
- `model` (string|null): Livewire model binding; default falls back to `wire:model` attribute
- `disabled` (bool)
- `id` (string|null): optional custom input id

### Behavior & states
- Uses a **screen-reader-only** native checkbox (`sr-only`) and a styled label.
- Uses Tailwind’s `peer` / `peer-checked` selectors to animate:
  - border/background change on checked
  - check icon scale/opacity transitions
- Supports `wire:key` passthrough on wrapper for stable DOM diffing.

### Usage examples
**Boolean model**
- Bind a boolean field with `wire:model="filters.only_active"`.

**Multi-select (array model)**
- Bind an array with `wire:model="filters.statuses"` and set `value="New"`, `value="Routed"`, etc.

> Note: For Livewire array binding, make sure the model is an array and values are unique.

## Suggested component catalog (next)
If you want the admin panel to be consistent, these component types are worth standardizing next:
- Status badge/chip (`cst_status_chip`)
- Table header + sort control (`cst_table_sort`)
- Filter popover (`cst_filter_panel`)
- Empty state (`cst_empty_state`)
- Confirm modal wrapper (`cst_confirm`)

---

# Screenshots (placeholders)
Add screenshots to make the UI understandable in 10 seconds.

Recommended folder:
- `docs/screenshots/`

Placeholders:
- `docs/screenshots/dashboard.png`
- `docs/screenshots/rfqs-index.png`
- `docs/screenshots/rfq-show.png`
- `docs/screenshots/quote-compare.png`

---

# Architecture notes

## Key entry points
- Routes: `routes/web.php`, `routes/supplier.php`
- Livewire screens: `app/Livewire/**` + `resources/views/livewire/**`
- Models: `app/Models/**`
- Statuses/enums: `app/Enums/RequestStatus.php`
- Workflow events: `app/Events/**` and docs in `docs/workflow-events-system.md`

## Domain language (glossary)
- **RFQ / Request**: internal purchase request used as the start of procurement
- **Request Item**: line item (product, qty, spec)
- **Supplier Invitation**: record of sending a request to a supplier
- **Quote**: supplier pricing response
- **Award**: selecting a quote and converting to an order/PO (target flow)

---

# Local development reveal (quick)
See also `docs/project-readme.md` for full setup.

- Copy env and install deps
- Run migrations/seed
- Start Vite + Laravel server

---

# Contribution notes
- Prefer adding UI using **existing tokens and patterns** rather than inventing new styles per page.
- When adding a component, document:
  - props
  - states (default/hover/focus/disabled/error)
  - Livewire usage (model binding, events)
  - accessibility notes

---

# Roadmap (design system)
- Component catalog page under `docs/` (or a Livewire “Style Guide” route)
- Standardize form field wrappers (label/help/error)
- Add visual regression snapshots for core components
