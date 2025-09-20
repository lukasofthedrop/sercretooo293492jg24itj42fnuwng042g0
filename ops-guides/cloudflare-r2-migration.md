# Cloudflare R2 Migration Playbook

This document captures the exact steps for moving the LucrativaBet public assets from the local `storage/app/public` tree to an external Cloudflare R2 bucket backed by the CDN.

## 1. Provision the R2 bucket

1. **Create an API token** inside Cloudflare with the following permissions:
   - Account level: `Workers R2 Storage` → `Edit`
   - Allow access to the account that will host the `lucrativa-storage` bucket.
2. Note the following values (they map directly to the AWS-compatible environment variables):
   - `R2_ACCOUNT_ID` – visible in the R2 dashboard URL (`https://dash.cloudflare.com/<account_id>/r2/...`).
   - `R2_ACCESS_KEY_ID` / `R2_SECRET_ACCESS_KEY` – generated when creating the API token with S3 compatibility.
3. Create the bucket via API or UI. API example:

   ```bash
   curl -X POST \
     -H "Authorization: Bearer ${CLOUDFLARE_API_TOKEN}" \
     -H "Content-Type: application/json" \
     "https://api.cloudflare.com/client/v4/accounts/${R2_ACCOUNT_ID}/r2/buckets" \
     -d '{"name":"lucrativa-storage"}'
   ```

   Repeatability note: the call is idempotent. A `409` response means the bucket already exists.

## 2. Upload the existing assets

1. Ensure `storage_public.tar.xz` exists at the repository root. It was generated from the previous production assets and is ~52 MB.
2. Install the AWS CLI locally (`brew install awscli` on macOS).
3. Run the helper script (from the project root):

   ```bash
   chmod +x scripts/sync-public-storage-to-r2.sh
   R2_ACCOUNT_ID="<account>" \
   R2_ACCESS_KEY_ID="<access-key>" \
   R2_SECRET_ACCESS_KEY="<secret-key>" \
   R2_BUCKET="lucrativa-storage" \
   ./scripts/sync-public-storage-to-r2.sh
   ```

   The script unpacks the archive into `tmp/r2-sync`, pushes it to R2 with `aws s3 sync` (using the Cloudflare endpoint), and deletes anything in the bucket that is no longer present locally.

## 3. Wire the application to R2

1. Configure the environment (Railway variables):

   ```env
   FILESYSTEM_DISK=public
   AWS_ACCESS_KEY_ID=<access-key>
   AWS_SECRET_ACCESS_KEY=<secret-key>
   AWS_DEFAULT_REGION=auto
   AWS_BUCKET=lucrativa-storage
   AWS_ENDPOINT=https://<account-id>.r2.cloudflarestorage.com
   AWS_USE_PATH_STYLE_ENDPOINT=true
   CDN_URL=https://cdn.lucrativa.bet   # replace with the DNS record you expose via Cloudflare
   ```

2. Create a DNS record in Cloudflare (e.g. `cdn.lucrativa.bet`) pointing to the R2 public bucket using the **R2 Custom Domains** feature. This gives you a friendly hostname and TLS for free. Once set, update `CDN_URL` accordingly.
3. On Railway, redeploy the application. With the updated configuration the Laravel `public` disk now points to the CDN-backed bucket and all previously hardcoded `/storage/...` URLs resolve via `Storage::disk('public')->url()`.

## 4. Post-migration validation

- Visit `https://cdn.lucrativa.bet/icon/icon-padrao.webp` (or the hostname chosen) to confirm assets are reachable.
- Hit the legacy path `https://lucrativabet-web-production.up.railway.app/storage/icon/icon-padrao.webp`; it now issues a redirect to the CDN thanks to the new route shim, keeping old links functional.
- Upload a new file from the admin panel and verify it appears in the R2 bucket (look for its key under the corresponding prefix).

## 5. Ongoing operations

- Keep the `storage_public.tar.xz` archive outside of Git (it is gitignored); regenerate it only if you need a full snapshot backup.
- For CI/CD, add the sync script as a build step if you need to automatically mirror assets after pipeline runs.
- Monitor Cloudflare analytics for cache HIT ratios. Consider enabling cache rules (`Cache Reserve`, tiered cache, or custom cache keys) once traffic ramps up.
