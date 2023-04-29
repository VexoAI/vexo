#!/bin/sh

vendor/bin/phpunit --coverage-text --coverage-html tmp/coverage && \
vendor/bin/phpstan analyse && \
vendor/bin/php-cs-fixer fix --cache-file=tmp/.php-cs-fixer.cache --allow-risky=yes --dry-run
