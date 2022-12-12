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
   * Fix the login link domain to the public URL.
   *
   * In case you're using Docker or a reverse proxy, the domain used by the
   * backend server to access the destination might be different from the
   * domain which is publicly-available. Fix it here.
   *
   * @param string $loginLink
   *   A login link with the potentially wrong domain.
   *
   * @return string
   *   The login link with the domain fixed.
   */
  public function fixLoginLinkDomain(string $loginLink) : string;

  /**
   * Get the public URL.
   *
   * @return string
   *   The public URL.
   */
  public function getPublicUrl() : string;

  /**
   * Return the integration UUID.
   *
   * @return string
   *   The integration UUID.
   */
  public function getIntegrationUuid() : string;

}
