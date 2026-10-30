# Official PHP 8.2.35 cli image on bookworm, plus an OS package upgrade and the CVE-2026-115560 mitigation.
# It already contains the CVE-2026-109618 / CVE-2026-109619 fix.
FROM php:8.2.35-cli-bookworm

# upgrade OS packages from the base image to pick up security fixes
RUN set -eux; \
	export DEBIAN_FRONTEND=noninteractive; \
	apt-get update; \
	apt-get upgrade -y; \
	rm -rf /var/lib/apt/lists/*

# CVE-2026-115560 mitigation: session upload progress is off by default (no upstream fix yet).
# The file is named 00-* so it loads first and any later conf.d file overrides it.
# To turn it back on, mount or add a file such as conf.d/zz-enable-upload-progress.ini
# containing "session.upload_progress.enabled = 1", or delete this file.
RUN set -eux; \
	{ \
		echo '; CVE-2026-115560 mitigation, see the Dockerfile comment for how to re-enable'; \
		echo 'session.upload_progress.enabled = 0'; \
	} > "$PHP_INI_DIR/conf.d/00-lineaje-session-upload-progress.ini"
