<?php

namespace Drupal\multiaccess\RoleMapping;

use Drupal\multiaccess\Utilities\DependencyInjectionTrait;

/**
 * Tracks role mapping.
 */
class RoleMapping implements RoleMappingInterface {

  use DependencyInjectionTrait;

  /**
   * The role-mapping array.
   *
   * @var array
   */
  protected $roleMapping;

  /**
   * Validate a role mapping array, throw an \Exception if it does not validate.
   *
   * @param array $roleMapping
   *   A role-mapping array.
   */
  public function validateRoleMappingArray(array $roleMapping) {
    foreach ($roleMapping as $key => $value) {
      // The source role might exist on the source, but we at the destination
      // have no way of knowing, so just validate that it's in a valid format.
      $this->validateRoleFormat($key);
      foreach ($value as $remote_role) {
        // Validate that the role on the destination, this site, is actually
        // a valid role.
        $this->validateRole($remote_role);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function sourceToDestinationRoles(array $sourceRoles) : array {
    $return = [];

    foreach ($this->roleMapping as $source => $destinationsRoles) {
      if (in_array($source, $sourceRoles)) {
        $return = array_merge($return, $destinationsRoles);
      }
    }

    if (empty($return)) {
      throw new \Exception('Source roles ' . implode(', ', $sourceRoles) . ' do not map to any roles on the destination. Aborting.');
    }

    return array_combine($return, $return);
  }

  /**
   * Validate that a role exists on this site.
   *
   * @param string $role
   *   A role ID.
   */
  public function validateRole(string $role) {
    $this->validateRoleFormat($role);
    $all = $this->roleMappingFactory()->allRolesExceptAnonymous();
    if (!in_array($role, $all)) {
      throw new \Exception($role . ' is not a valid role; try ' . implode(', ', $all) . '.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function format() : array {
    $return = [];
    foreach ($this->roleMapping as $local_role => $remote_roles) {
      foreach ($remote_roles as $remote_role) {
        $return[] = '["role_mapping"]["' . $local_role . '"][] = ' . '"' . $remote_role . '";';
      }
    }
    return $return;
  }

  /**
   * Validate that a role has the correct format.
   *
   * @param string $role
   *   A role ID.
   */
  public function validateRoleFormat(string $role) {
    if (!trim($role)) {
      throw new \Exception('Role name cannot be empty.');
    }
  }

  /**
   * Constructor.
   *
   * @param array $roleMapping
   *   A role-mapping array, like [source_role => [destination_role]].
   */
  public function __construct(array $roleMapping) {
    $this->validateRoleMappingArray($roleMapping);
    $this->roleMapping = $roleMapping;
  }

}
