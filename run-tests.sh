#!/bin/bash

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BIN_DIR="$SCRIPT_DIR/vendor/bin"
TMP_DIR="$SCRIPT_DIR/tmp"

phpcs_options="--dry-run"
rector_options="--dry-run"

while (( "$#" )); do
  case "$1" in
    --fix-cs)
      phpcs_options=""
      shift
      ;;
    --fix-rector)
      rector_options=""
      shift
      ;;
    *)
      echo "Error: Invalid argument"
      exit 1
      ;;
  esac
done

$BIN_DIR/phpunit --coverage-text --coverage-html $TMP_DIR/coverage && \
$BIN_DIR/phpstan analyse && \
$BIN_DIR/php-cs-fixer fix --cache-file=$TMP_DIR/.php-cs-fixer.cache --allow-risky=yes --verbose --diff $phpcs_options &&
$BIN_DIR/rector process $rector_options
