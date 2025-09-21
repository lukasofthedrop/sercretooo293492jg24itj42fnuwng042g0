#!/usr/bin/env bash
set -euo pipefail

# Usage:
#   R2_ACCOUNT_ID=xxxx \
#   R2_ACCESS_KEY_ID=xxxx \
#   R2_SECRET_ACCESS_KEY=xxxx \
#   CDN_URL=https://cdn.example.com \
#   ./scripts/sync-public-storage-to-r2.sh

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ARCHIVE_PATH="${PROJECT_ROOT}/storage_public.tar.xz"
TMP_DIR="${PROJECT_ROOT}/tmp/r2-sync"
R2_BUCKET="${R2_BUCKET:-lucrativa-storage}"
R2_ACCOUNT_ID="${R2_ACCOUNT_ID:?R2_ACCOUNT_ID is required}"
R2_ACCESS_KEY_ID="${R2_ACCESS_KEY_ID:?R2_ACCESS_KEY_ID is required}"
R2_SECRET_ACCESS_KEY="${R2_SECRET_ACCESS_KEY:?R2_SECRET_ACCESS_KEY is required}"
R2_ENDPOINT="https://${R2_ACCOUNT_ID}.r2.cloudflarestorage.com"

if ! command -v aws >/dev/null 2>&1; then
  echo "aws CLI is required. Install with 'brew install awscli' or your package manager." >&2
  exit 1
fi

if [ ! -f "${ARCHIVE_PATH}" ]; then
  echo "Archive ${ARCHIVE_PATH} not found. Ensure storage_public.tar.xz exists." >&2
  exit 1
fi

rm -rf "${TMP_DIR}"
mkdir -p "${TMP_DIR}"

tar -xJf "${ARCHIVE_PATH}" -C "${TMP_DIR}"
SOURCE_DIR="${TMP_DIR}/storage/app/public"

if [ ! -d "${SOURCE_DIR}" ]; then
  echo "Unexpected archive layout: ${SOURCE_DIR} missing." >&2
  exit 1
fi

AWS_ACCESS_KEY_ID="${R2_ACCESS_KEY_ID}" \
AWS_SECRET_ACCESS_KEY="${R2_SECRET_ACCESS_KEY}" \
aws s3 sync "${SOURCE_DIR}/" "s3://${R2_BUCKET}" \
  --endpoint-url "${R2_ENDPOINT}" \
  --acl public-read \
  --follow-symlinks \
  --delete

echo "Upload complete. Remember to set CDN_URL to the hostname that fronts the bucket."