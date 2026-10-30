# Official PHP 8.3.36 fpm image on alpine3.24, plus an OS package upgrade and the CVE-2026-115560 mitigation.
# It already contains the CVE-2026-109618 / CVE-2026-109619 fix.
FROM php:8.3.36-fpm-alpine3.24

# upgrade OS packages from the base image to pick up security fixes
# (includes brotli-libs 1.2.1-r0, which fixes CVE-2026-111497; the base image has 1.2.0)
RUN apk upgrade --no-cache

# CVE-2026-115560 mitigation: session upload progress is off by default (no upstream fix yet).
# The file is named 00-* so it loads first and any later conf.d file overrides it.
# To turn it back on, mount or add a file such as conf.d/zz-enable-upload-progress.ini
# containing "session.upload_progress.enabled = 1", or delete this file.
RUN set -eux; \
	{ \
		echo '; CVE-2026-115560 mitigation, see the Dockerfile comment for how to re-enable'; \
		echo 'session.upload_progress.enabled = 0'; \
	} > "$PHP_INI_DIR/conf.d/00-lineaje-session-upload-progress.ini"
