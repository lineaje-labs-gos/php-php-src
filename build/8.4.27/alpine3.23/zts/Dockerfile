# Official PHP 8.4.27 zts image on alpine3.23, unmodified. It already contains the
# CVE-2026-109618 / CVE-2026-109619 fix; only the published name changes.
FROM php:8.4.27-zts-alpine3.23

# brotli-libs 1.2.0 in the base image is affected by CVE-2026-111497; fixed in 1.2.1-r0.
RUN apk upgrade --no-cache brotli-libs
