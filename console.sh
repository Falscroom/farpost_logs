#!/bin/bash
docker run -it --rm --init -v ${PWD}:/opt/workdir php:7.4-alpine $@