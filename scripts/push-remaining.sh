#!/bin/bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
git add -A
git status --short
git commit -m "feat: complete BindAdmin application source" || true
git push origin main
echo "Push complete."
