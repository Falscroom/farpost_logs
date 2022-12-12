-include make/local.mk
include make/docker.mk

PHP ?= php
COMPOSER ?= composer

test:
	@$(PHP) /opt/workdir/bin/phpunit -c /opt/workdir/config

install:
	@$(COMPOSER) install --working-dir=/opt/workdir