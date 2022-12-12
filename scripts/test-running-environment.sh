#!/bin/bash
#
# Run some checks on a running environment
#
set -e

echo '=> Running tests on a running environment.'

echo 'Make sure we can create a new integration'
docker-compose exec drupal_source /bin/bash -c './modules/custom/multiaccess/scripts/lib/docker-resources/create-test-integration.sh'

echo 'Log on to the source and get a unique login link for the destination'
docker-compose exec drupal_source /bin/bash -c 'drush ev "multiaccess_get_remote_uli_for_first_destination();"'

echo 'Test the running environment'
docker-compose exec drupal_source /bin/bash -c 'drush ev "multiaccess_selftest();"'
