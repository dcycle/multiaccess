<?php

namespace Drupal\multiaccess\RoleMapping;

use Drupal\multiaccess\Utilities\DependencyInjectionTrait;

/**
 * Role mapping factory.
 */
class RoleMappingFactory implements RoleMappingFactoryInterface {

  use DependencyInjectionTrait;

  /**
   * {@inheritdoc}
   */
  public function allRolesExceptAnonymous() : array {
    $all = $this->entityTypeManager()->getStorage('user_role')->loadMultiple();

    unset($all['anonymous']);

    return array_keys($all);
  }

}
