# n8n Workflow Design – Authentication & 2FA

## Objective
Port Laravel's authentication stack (`routes/groups/api/auth/auth.php` + 2FA routes) into an n8n-based REST workflow while preserving:
- JWT issuance and refresh logic (tymon/jwt-auth equivalent)
- Registration, login, logout, forget/reset password flows
- Authenticated profile retrieval (`me`/`verify`)
- Two-factor enable/disable/verify endpoints

## Data / Services
- **DB**: MySQL (tables `users`, `password_resets`, `two_factor_settings`, etc.)
- **Mail**: SMTP credentials from `.env` (redacted)
- **JWT Secret**: Mirror current `JWT_SECRET`
- **Rate limiting / throttle**: replicate via n8n Function + Redis if needed

## Workflow Nodes (per endpoint)
1. **HTTP Trigger** – One per REST endpoint (`POST /login`, `POST /register`, ...)
2. **Function (Validation)** – Validate payload, enforce required fields
3. **MySQL Node** – Query/Update user records, password reset tokens
4. **Function (Hash / JWT)** – Use n8n Function to verify password (bcrypt) and mint JWT via `jsonwebtoken` module (environment var `JWT_SECRET`)
5. **If Nodes** – Decide flows (user found, passwords match, token valid)
6. **Email Send** – For forget-password
7. **Set Response** – Craft API response payloads
8. **Error Handling** – Global workflow error hook logging to memory-bank MCP / external log

## Endpoints Mapping
| Endpoint | Method | Notes |
| --- | --- | --- |
| `/api/auth/login` | POST | Validate credentials, create JWT (access + refresh). Update `last_login_at` |
| `/api/auth/register` | POST | Insert user, default wallet, send welcome email |
| `/api/auth/forget-password` | POST | Create reset token, send email |
| `/api/auth/reset-password/{token}` | POST | Validate token, reset password |
| `/api/auth/verify` | GET | Verify JWT (Authorization header) |
| `/api/auth/refresh` | POST | Validate refresh token, roll access token |
| `/api/auth/logout` | POST | Invalidate refresh token entry |
| `/api/auth/me` | POST | Return user profile |
| `/2fa/*` | VAR | Wrap existing flow (enable/disable/verify) with TOTP using n8n code node |

## Security Considerations
- Use HTTPS via Railway reverse proxy
- Store hashed refresh tokens if required
- Apply per-IP throttle logic with Redis/n8n cache
- Mirror existing friendly error messages

## Testing Checklist
- Unit-test each endpoint with Postman collection
- Negative cases: wrong password, inactive user, expired reset token, invalid 2FA code
- Integration path: register -> enable 2FA -> login -> refresh -> logout -> reset password

## Deployment Notes
- Use `memory-bank` MCP to log decisions
- Manage Railway environment (`railway-mcp`) to set secrets (DB, JWT, SMTP)
- Add n8n credentials for MySQL + SMTP at workflow level
