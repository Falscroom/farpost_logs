PHP_BUILD_IMAGE = php:7.4-alpine
COMPOSER_BUILD_IMAGE = composer:2.4.4

ifneq ($(OS),darwin)
DOCKER_IP = host.docker.internal
else
DOCKER_IP = $(shell docker network inspect bridge -f '{{ range .IPAM.Config }}{{ .Gateway }}{{ end }}')
endif

DOCKER_RUN = docker run -it --rm \
	-v $(PWD):/opt/workdir \
	-e GOSU=yes \

PHP_PARAMS = -e PHP_CONF_MEMORY_LIMIT=512M

PHP = $(DOCKER_RUN) $(PHP_PARAMS) $(PHP_BUILD_IMAGE) php
COMPOSER = $(DOCKER_RUN) $(PHP_PARAMS) $(COMPOSER_BUILD_IMAGE) composer
