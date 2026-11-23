# Official PHP 8.2.36 zts image on alpine3.23, plus an OS package upgrade.
# It already contains the CVE-2026-109618 / CVE-2026-109619 fix.
ARG PHP_VERSION=8.2.36
FROM php:${PHP_VERSION}-zts-alpine3.23

# upgrade OS packages from the base image to pick up security fixes
# (includes brotli-libs 1.2.1-r0, which fixes CVE-2026-111497; the base image has 1.2.0)
RUN apk upgrade --no-cache
