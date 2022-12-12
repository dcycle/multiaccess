#!/bin/bash
#
# This script is run when the Drupal docker container is ready. It prepares
# an environment for development or testing, which contains a full Drupal
# installation with a running website.
#
set -e

if [ -z "$1" ]; then
  >&2 echo "Please include source or destination"
  exit 1
fi

TRIES=20
echo "$1: Will try to connect to MySQL container mysql_$1 until it is up. This can take up to $TRIES seconds if the container has just been spun up."
OUTPUT="ERROR"
for i in $(seq 1 "$TRIES");
do
  OUTPUT=$(echo 'show databases'|{ mysql -h mysql_"$1" -u root --password=drupal 2>&1 || true; })
  if [[ "$OUTPUT" == *"ERROR"* ]]; then
    sleep 1
    echo "Try $i of $TRIES. MySQL container is not available yet. Should not be long..."
  else
    echo "MySQL is up! Moving on..."
    break;
  fi
done

if [[ "$OUTPUT" == *"ERROR"* ]]; then
  >&2 echo "Server could not connect to MySQL after $TRIES tries. Abandoning."
  >&2 echo "$OUTPUT"
  exit 1
fi

drush si -y --db-url "mysql://root:drupal@mysql_$1/drupal" minimal
ls -lah /var/www/html/sites/default
chmod +w /var/www/html/sites/default/settings.php
cat /var/www/html/modules/custom/multiaccess/scripts/lib/docker-resources/add-to-settings.txt >> /var/www/html/sites/default/settings.php
chmod -w /var/www/html/sites/default/settings.php
drush en -y multiaccess multiaccess_uli_ui
