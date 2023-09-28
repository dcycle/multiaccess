#!/bin/bash
#
# Fix PHP code.
#
set -e

echo 'Fixing PHP style errors with https://github.com/dcycle/docker-php-lint'
echo ''

docker run --rm \
  -v "$(pwd)":/code \
  --entrypoint=/vendor/bin/phpcbf \
  dcycle/php-lint:3 \
  --extensions=php,module,install,inc \
  --standard=DrupalPractice \
  /code
docker run --rm \
  -v "$(pwd)":/code \
  --entrypoint=/vendor/bin/phpcbf \
  dcycle/php-lint:3 \
  --extensions=php,module,install,inc \
  --standard=Drupal \
  /code

echo ' => WE FIXED WHAT WE COULD!'
echo ''
