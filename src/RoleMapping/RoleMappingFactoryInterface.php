<?php

namespace Drupal\multiaccess\RoleMapping;

/**
 * Interface for a role mapping factory.
 */
interface RoleMappingFactoryInterface {

  /**
   * Get all roles except anonymous, as an array of strings.
   *
   * @return array
   *   All roles except anonymous, as an array of strings.
   */
  public function allRolesExceptAnonymous() : array;

}
