# Repository Guidelines

## Project Structure & Module Organization
The Laravel core lives in `app/`, with HTTP controllers, Filament admin resources, services, and jobs grouped by feature. Blade views, Vue-powered widgets, and Tailwind assets reside in `resources/`, while production-ready static assets are compiled into `public/`. Database migrations, seeders, and factories stay in `database/`. Tests are split between `tests/Feature`, `tests/Unit`, and Playwright specs in `tests/e2e`.

## Build, Test, and Development Commands
Run `composer install` and `npm install` the first time you set up the project. Use `php artisan serve --port=8000` for a local server that matches the documented port. Front-end assets compile with `npm run dev` for hot reloads and `npm run build` for production bundles. Seed non-production data through `php artisan migrate --seed` to load the validated admin and casino fixtures.

## Coding Style & Naming Conventions
Follow PSR-12 for PHP; the `.editorconfig` enforces UTF-8, LF endings, and 4-space indentation. Keep class names in StudlyCase, controllers under `App\Http\Controllers`, and Livewire/Filament components grouped by feature folder. Vue and JavaScript modules should export camelCase utilities and PascalCase components. Run `./vendor/bin/pint` before committing backend code, and keep compiled assets (`public/`, `storage/`) out of version control.

## Testing Guidelines
Backend coverage relies on PHPUnit; execute `php artisan test` or `php artisan test --filter=Feature\Affiliate` when iterating. Store new feature tests as `SomethingTest.php` within the relevant Feature or Unit namespace. Browser flows are validated with Playwright via `npm run test:e2e`; add specs to `tests/e2e/*.spec.ts` and prefer explicit selectors over text-based ones. Aim to extend existing fixtures rather than creating ad-hoc states, and document any required env toggles in the spec header.

## Commit & Pull Request Guidelines
Recent history favors concise, imperative summaries such as `Fix admin redirect loop` or `Add affiliate ledger report`. Reference related issues in the body, describe database or config impacts, and note manual test steps performed. Pull requests should include a short context paragraph, screenshots or terminal output when UI or console behavior changes, and confirmation that Playwright and PHPUnit suites pass locally.

## Security & Configuration Tips
Never commit `.env*`, SQL backups, or secrets; use the provided examples in `env.railway.example` when documenting changes. Respect the existing rate-limiting, CSP, and 2FA middleware—adjustments belong in targeted PRs with security review notes. When touching queue, cache, or broadcasting settings, coordinate with ops to update the matching files in `deploy/` and ensure `storage/logs/` stays gitignored.
