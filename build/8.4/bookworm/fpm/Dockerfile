# Official PHP 8.4.28 fpm image on bookworm, plus an OS package upgrade.
# It already contains the CVE-2026-109618 / CVE-2026-109619 fix.
ARG PHP_VERSION=8.4.28
FROM php:${PHP_VERSION}-fpm-bookworm

# upgrade OS packages from the base image to pick up security fixes
RUN set -eux; \
	export DEBIAN_FRONTEND=noninteractive; \
	apt-get update; \
	apt-get upgrade -y; \
	rm -rf /var/lib/apt/lists/*
