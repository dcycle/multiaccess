#!/bin/bash
#
# Get a login link for the environment.
#
set -e

echo "=> Source site:"
docker-compose exec -T drupal_source /bin/bash -c "drush -l $(docker-compose port webserver_source 80) uli"
echo "=> Destination site:"
docker-compose exec -T drupal_destination /bin/bash -c "drush -l $(docker-compose port webserver_destination 80) uli"
