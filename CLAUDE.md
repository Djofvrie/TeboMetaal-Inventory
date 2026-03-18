# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Inventory management webapp for Tebo Metaal (Dutch metal supplier). Public interface for browsing products and booking stock removals, admin panel for managing inventory. All UI text is in Dutch.

## Commands

```bash
# Development
composer run dev          # Starts Laravel server, queue, logs, and Vite concurrently
php artisan serve              # Manual server start (default port 8000)

# Database
php artisan migrate:fresh --seed  # Reset DB with demo data (admin@tebo.nl / password)
php artisan migrate               # Run pending migrations

# Frontend
npm run build             # Production build (Tailwind + JS)
npm run dev               # Vite watch mode

# Testing
php artisan test          # Run PHPUnit tests
```

## Architecture

**Stack:** Laravel 13 / PHP 8.3+ / SQLite (local) / Blade + Tailwind CSS + Alpine.js / Vite

### Data Model

```
Category (Koker, Buis, Strip, etc.)
  └─ Product (e.g. "Koker 60 x 60", dimension: "60x60")
      └─ ProductVariant (wall_thickness + quality, e.g. 4mm S235)
          ├─ StockItem (length_mm + quantity, e.g. 5x 6000mm)
          └─ StockMutation (audit log of all changes)
```

Steel qualities: S235, S275, S355. Lengths stored in mm internally, displayed in meters on frontend.

### Route Structure

**Public** (no auth): `/` → `/categorie/{slug}` → `/product/{product}` → `/variant/{variant}` (browse + deduct stock). Search at `/zoeken`.

**Admin** (auth middleware, `/admin` prefix): Dashboard, CRUD for categories/products, stock management, CSV import, mutation log. Routes use `admin.` name prefix.

**Key route file:** `routes/web.php` contains all routes. Auth routes in `routes/auth.php` (Breeze).

### Controllers

- `PublicController` — Category browsing, product/variant detail, stock deduction (cut mode + full piece mode), search
- `Admin\StockController` — Dashboard, stock overview/edit, CSV import, inventory reset
- `Admin\CategoryController` — CRUD (views use shared `form.blade.php` for create/edit)
- `Admin\ProductController` — CRUD with dynamic variant management
- `Admin\MutationController` — Filterable audit log

### Views

- `resources/views/public/` — Public pages using `<x-layouts.public>` component
- `resources/views/admin/` — Admin pages using `<x-app-layout>` (Breeze)
- `resources/views/components/layouts/public.blade.php` — The actual public layout component (also mirrored at `layouts/public.blade.php`)
- `resources/views/components/category-icon.blade.php` — SVG icons per category slug

### Stock Deduction Logic

Two modes in `PublicController@deduct`:
1. **Cut mode** (`mode=cut`): User picks source piece + desired length in mm. Creates remainder piece.
2. **Full piece** (`mode=full`): User picks piece + quantity. Removes whole pieces.

Both log a `StockMutation` of type `removal`. Stock items with same `length_mm` are grouped in the UI but may exist as separate DB rows.

## Conventions

- Models use `$fillable`, relationship methods with return types, custom accessor methods
- Database transactions for all stock mutations (`DB::transaction`)
- Dutch locale (`APP_LOCALE=nl`), Dutch text in all views
- Admin forms validate with Laravel's `$request->validate()`
- Category icons determined by slug via `<x-category-icon :slug="..." />`
- Seeder contains full real product data for Koker category, realistic demo data for others
