# Official PHP 8.2.36 apache image on trixie, plus an OS package upgrade.
# It already contains the CVE-2026-109618 / CVE-2026-109619 fix.
ARG PHP_VERSION=8.2.36
FROM php:${PHP_VERSION}-apache-trixie

# upgrade OS packages from the base image to pick up security fixes
RUN set -eux; \
	export DEBIAN_FRONTEND=noninteractive; \
	apt-get update; \
	apt-get upgrade -y; \
	rm -rf /var/lib/apt/lists/*
