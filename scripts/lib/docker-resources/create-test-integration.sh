#!/bin/bash
#
# Create a test integration.
#
set -e

echo "Make sure /do-not-commit exists on the container"
if [ ! -d /do-not-commit ]; then
  >&2 echo "/do-not-commit does not exist on the container, although it should because it is shared in docker-compose.yml. This might have happened if you deleted ./do-not-commit on the host while containers are running. Please run ./scripts/destroy.sh and rm -rf ./do-not-commit, then re-run your deployment and/or tests."
  exit 1
fi

mkdir -p /do-not-commit/test-integration/
echo '<?php' > /do-not-commit/test-integration/unversioned-settings.php
{
  echo "";
  echo "/**";
  echo " * @file";
  echo " * File created for testing purposes.";
  echo "";
  echo " * This was created automatically by the MultiAccess module's";
  echo " * ./scripts/lib/docker-resources/create-test-integration.sh script";
  echo " * during automated testing. It can be safely deleted.";
  echo " */";
} >> /do-not-commit/test-integration/unversioned-settings.php
drush ev "multiaccess_new_integration(label: 'Test Destination Site', public: 'http://webserver_destination', internal: 'http://webserver_destination', role_mapping_array: ['authenticated' => ['authenticated']])" >> /do-not-commit/test-integration/unversioned-settings.php
