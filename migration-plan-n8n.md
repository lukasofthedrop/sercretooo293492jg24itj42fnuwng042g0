# LucrativaBet → n8n Migration Blueprint

## 1. Core Services & Data Sources
- **Primary DB**: MySQL (`.env` → DB_* vars)
- **Cache/Queue**: Redis (cache + queue + session drivers)
- **File Storage**: Local (`storage/`, `public/` symlink)
- **External APIs**:
  - AureoLink: payments (deposit, withdrawal, webhook, status)
  - PlayFiver: game launch/webhook (via helper trait/controllers)
  - Email/SMTP: password reset notifications
- **Auth**: JWT (tymon/jwt-auth), Sanctum (csrf), 2FA controllers

## 2. Functional Domains → Target n8n Workflows

| Domain | Current Laravel Entry Points | n8n Workflow Goal |
| --- | --- | --- |
| Authentication | `api/auth/login, register, forget-password, reset-password, refresh, logout, me`, 2FA routes | Build REST workflows handling JWT issuance/verification, user onboarding, password recovery; integrate with existing user table |
| User Profile | `api/profile` routes (avatar upload, language, name) | Map to n8n flows for profile CRUD + file handling |
| Wallet & Affiliates | `api/profile/wallet`, `api/profile/affiliates`, `AffiliateController@painelAfiliado`, `AffiliateWithdrawalController` | Model balances, commissions, revshare calculations, withdrawal logic |
| Payments (AureoLink) | `aureolink/webhook`, `deposit`, `withdrawal`, `transaction/{id}/status` | Create webhook-triggered flows, manage deposit/withdraw tasks, status checks |
| Games/PlayFiver | `api/jogos/*`, `GameController@sourceProvider`, `playfiver/webhook` | Orchestrate game sessions, provider integration, track views, ensure wallet checks |
| Missions & VIP | `api/missions`, `api/vips` | Recreate progress tracking and reward redemption |
| Admin (Filament) | Many `admin/*` routes | Determine which features migrate vs remain in Laravel admin |
| Utilities | `/clear`, `/optimize-system`, etc. | Automate maintenance tasks in n8n (optional) |

## 3. Migration Strategy
1. **Dependency Inventory**
   - Dump `.env` (sanitized) for db/cache credentials
   - Export database schema (migrations) for n8n data models
   - Inventory queue jobs/events (currently none queued)

2. **Sequential Workflow Porting**
   1. Auth / JWT / 2FA flows
   2. Wallet + Affiliate metrics & withdrawal
   3. Payments (AureoLink deposit/withdrawal + webhook)
   4. Game orchestration (PlayFiver)
   5. Missions/VIP progress
   6. Remaining profile/settings endpoints

3. **Data Migration**
   - Export relevant tables (users, wallets, affiliate settings/history, games, transactions, missions, VIP) via SQL
   - Import into n8n data store (Postgres/MySQL via Railway) or continue referencing existing DB if feasible

4. **Automation Hooks**
   - Configure n8n HTTP endpoints mirroring existing Laravel routes
   - Set up scheduled jobs (leaderboard updates, cache clears)
   - Integrate Redis replacement (n8n queues or external)

5. **Testing & Cutover**
   - For each workflow: unit test with sample payloads (use Postman/n8n tester)
   - Run end-to-end scenario (user registration -> deposit -> play -> commission)
   - Update DNS/proxy to point to n8n endpoints once validated

## 4. Outstanding Items Before Execution
- Resolve PSR-4 warnings (rename `DashboardAdmin.php`, `LayoutCssCustom.php`, `JwtMiddleWare.php`)
- Free port 8000 (stop legacy `artisan serve` instances) for local validation when needed
- Document external service credentials (AureoLink, JWT secret, PlayFiver keys)
- Snapshot current DB & storage for rollback

## 5. MCP & Tooling Utilization
- `memory-bank` & `memory-palace`: log migration decisions / knowledge snapshots
- `github`: track repo state & commits during transition
- `railway-mcp`: manage Railway env (databases, n8n deployment)
- `desktop-commander`, `playwright-mcp`: local file ops, UI automation tests
- `servers` MCP: environment info & process orchestration

## 6. Next Steps
1. Prepare sanitized env/config export
2. Design n8n workflows per domain (start with Auth)
3. Set up Railway resources (DB, n8n instance) via MCP
4. Implement & test each workflow sequentially
5. Plan final cutover window and rollback strategy

