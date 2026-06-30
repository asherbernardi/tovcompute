# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**tovcompute** (branded "Anson Analytics") is a Laravel 11 application that analyzes qualitative investment factors by tracking companies and their charitable giving. Core workflow: ingest company data → match companies to charities → review/approve matches → analyze giving history.

## Commands

### PHP / Laravel
```bash
php artisan migrate          # Run database migrations
php artisan tinker           # Interactive REPL
vendor/bin/phpunit           # Run all tests
vendor/bin/phpunit --filter TestName   # Run a single test
vendor/bin/pint              # Format PHP code (Laravel Pint)
```

### Frontend
```bash
npm run dev      # Watch and rebuild assets (development)
npm run build    # Production build
```

### Database
- MySQL, database `laravel_db`, user `asher` on localhost:3306
- Credentials in `.env`

## Architecture

### Data Model
```
Company (ticker, EIN, secID)
  ├── has many Charity (EIN, parent FK → company.id)
  │     └── has many CharityGiving (tax_year, grants_paid, net_assets, etc.)
  └── many-to-many List (via list_associate pivot)

CharityRecommend — links Company + Charity with match% and status (0=pending, 1=confirmed, 2=rejected)
```

**Important:** The app uses Unix integer timestamps throughout — models map `CREATED_AT`/`UPDATED_AT` to an `updated` column and cast dates with `'U'` format. Laravel's default datetime handling does not apply here.

### Key Controllers & Their Roles
- **CompanyController** — CRUD, sorting/filtering/searching (1000 records/page), CSV export, SEC API fetch (`company_tickers.json`)
- **CharityController** — CRUD, company association/disassociation, CSV export; uses HTMX for partial table updates
- **MatchController** — Matching algorithm: strips legal suffixes (Inc, LLC, Corp…), then applies Levenshtein distance + SOUNDEX for phonetic similarity; writes `CharityRecommend` records
- **CharityRecommendController** — Approval workflow for recommendations with match ≥ 70%
- **ListController** — Named lists (max 15-char names) grouping companies
- **CharityGivingController** — Syncs `charityID` in `charity_giving` by matching EINs
- **BackupController** — Triggers `spatie/laravel-backup` in the background

### Frontend Stack
- **Blade** templates with **Tailwind CSS**, **Alpine.js**, and **HTMX**
- **Livewire 3** (`AddCharity` component) for reactive UI
- Assets compiled via **Vite** (primary) with a legacy `webpack.mix.cjs` also present

### Incomplete / Work-in-Progress Areas
- `MatchController::saveMatchRecommendation` is commented out — recommendations are computed but not fully persisted
- `AddCharity` Livewire component has an empty `search()` method stub
- `ManualController` contains `exec()` calls to run PHP scripts directly — treat carefully
- Several `CharityGivingController` methods are stubs
