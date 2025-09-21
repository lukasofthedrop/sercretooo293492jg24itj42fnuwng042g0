# n8n Migration Checklist (LucrativaBet)

## Pre-Migration
- [x] Inventory modules & routes (auth, wallet, affiliates, AureoLink, PlayFiver, missions, VIP)
- [x] Generate sanitized env (`env.sanitized`)
- [x] Resolve PSR-4 violations (DashboardAdmin, LayoutCssCustom, JWTMiddleware)
- [ ] Snapshot MySQL & storage directories
- [ ] Export queue/cron configurations

## Environment Setup
- [ ] Provision Railway project (n8n + MySQL/Postgres if needed)
- [ ] Configure secrets via railway-mcp (DB creds, JWT, SMTP, AureoLink)
- [ ] Enable HTTPS and custom domain if applicable

## Workflow Implementation (n8n)
1. **Auth & 2FA** – `docs/n8n-auth-workflow.md`
2. **Wallet & Affiliate Metrics** – map commission calculations, withdrawal flow
3. **AureoLink Payments** – deposit/withdraw webhook handling
4. **Game Launch / PlayFiver** – session token generation, webhook ingest
5. **Missions & VIP** – progress tracking, reward payout
6. **Profile Settings & Utilities** – avatar upload, language, cache clears

## Data Migration / Integration
- [ ] Decide on DB strategy (reuse existing vs. migrate)
- [ ] Import required tables into new DB (if migrating)
- [ ] Set up Redis alternative (n8n queue, Upstash, etc.)

## Testing & Cutover
- [ ] Build Postman collection hitting n8n endpoints
- [ ] E2E scenario: register → deposit → play → commission → withdrawal
- [ ] Monitor logs (memory-bank MCP + n8n execution logs)
- [ ] Plan rollback (Laravel fallback)

## Post-Cutover
- [ ] Decommission Laravel services once stable
- [ ] Document new runbooks & hand-off
- [ ] Archive original repo snapshot
