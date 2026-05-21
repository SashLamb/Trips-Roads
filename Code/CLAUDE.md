# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**TripsAndRoads** is a CakePHP 5.x roadtrip planning and sharing web application (PHP 8.2+). Users can create roadtrips composed of ordered trips and sub-steps, share them publicly, comment, favorite, and export to PDF or GPX.

## Commands

```bash
# Start dev server (visit http://localhost:8765)
bin/cake server -p 8765

# Run all tests
composer test
# or
phpunit --colors=always

# Run a single test file
phpunit --colors=always tests/TestCase/Controller/RoadtripsControllerTest.php

# Code style check / fix
composer cs-check
composer cs-fix

# Run tests + style check together
composer check
```

## Architecture

Standard CakePHP MVC. Application bootstrap in `src/Application.php` registers middleware (CSRF, Authentication, BodyParser) and two plugins: `Authentication` and `CakePdf`.

### Data model hierarchy

```
Roadtrip (visibility: public|private)
  └── Trip (ordered, has departure/arrival city + transport_mode)
        └── SubStep (ordered, has city + optional lat/lon + duration)
              └── SubStepPhoto
```

Supporting entities: `User`, `Comment`, `Favorite`, `FavoritePlace`, `GeocodedPlace` (geocoding cache), `History` (view tracking), `Message`/`Conversation`, `Friendship` (status: pending|accepted), `PointsOfInterest`.

`AppController::beforeRender` injects `$isAdmin` into every view based on the authenticated user's `role` field.

### Geocoding

`RoadtripsController::_getCoordinates()` resolves city names to coordinates: it first checks the `geocoded_places` table, then falls back to the Nominatim (OpenStreetMap) API and caches the result. This is used when rendering roadtrip view maps and when building GPX exports.

### Map rendering

Leaflet.js (`webroot/js/map.js`). The controller serialises trip/sub-step data as JSON (`$jsMapDataJson`) and passes it to the template, which passes it to JavaScript.

### Authentication

Session + Form authenticators via `cakephp/authentication`. Google OAuth2 is handled in `UsersController::loginGoogle` / `callbackGoogle` using `league/oauth2-google`. Credentials configured in `config/app_local.php` under the `Google` key.

Unauthenticated access is allowed per-controller via `$this->Authentication->addUnauthenticatedActions([...])` in `beforeFilter`. Public roadtrip browsing (`index`, `publicRoadtrips`, `view`) does not require login.

### File uploads

Cover images for roadtrips are handled in `RoadtripsTable::beforeSave` and saved to `webroot/uploads/roadtrips/`. Sub-step photos are uploaded via AJAX in `RoadtripsController::uploadStepImage` and saved to `webroot/uploads/sousetapes/`.

### PDF export

`RoadtripsController::exportPdf` renders the `Roadtrips/export_pdf` template with the `layoutPdf` layout, converts to PDF with Dompdf, and returns it as a download.

## Configuration

Environment-specific settings live in `config/app_local.php` (not committed / gitignored in production): database credentials, Google OAuth client ID/secret, SMTP config, and the Gemini API key. Environment-agnostic settings are in `config/app.php`.

The test suite uses SQLite via `DATABASE_TEST_URL` (see `phpunit.xml.dist`).

## Code style

PHPStan level 8 (`phpstan.neon`). PHPCS uses the `CakePHP` ruleset (`phpcs.xml`), applied to `src/` and `tests/`. Return type hint rule is relaxed for controllers.

## Misc

- There are duplicate table files with a ` - Copie` suffix (e.g. `RoadtripsTable - Copie.php`) — these are accidental copies and should be ignored or deleted.
- The `fonctions/` directory contains only IntelliJ IDE metadata and no application code.
- `src/View/AjaxView.php` is used for JSON/AJAX responses; `src/View/Cell/MessageCell.php` renders the messaging sidebar.
- Routes use `DashedRoute`. The root `/` maps to `Roadtrips::index`.