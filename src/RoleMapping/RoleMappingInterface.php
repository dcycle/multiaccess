<?php

namespace Drupal\multiaccess\RoleMapping;

/**
 * Interface for role mapping.
 */
interface RoleMappingInterface {

  /**
   * Format to put into unversioned settings file.
   *
   * @return array
   *   The strings to put into the unversioned settings file.
   */
  public function format() : array;

  /**
   * Get destination roles from source roles.
   *
   * @param array $sourceRoles
   *   Source roles.
   *
   * @return array
   *   Destination roles.
   */
  public function sourceToDestinationRoles(array $sourceRoles) : array;

}
