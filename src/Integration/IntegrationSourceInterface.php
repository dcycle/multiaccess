<?php

namespace Drupal\multiaccess\Integration;

use Drupal\multiaccess\RoleMapping\RoleMappingInterface;

/**
 * Interface for an integration source. See README.md.
 */
interface IntegrationSourceInterface extends IntegrationHalfInterface, FormattableIntegrationInterface {

  /**
   * Return the role mapping.
   *
   * @return \Drupal\multiaccess\RoleMapping\RoleMappingInterface
   *   The role mapping.
   */
  public function getRoleMapping() : RoleMappingInterface;

  /**
   * Return the integration UUID.
   *
   * @return string
   *   The integration UUID.
   */
  public function getIntegrationUuid() : string;

}
