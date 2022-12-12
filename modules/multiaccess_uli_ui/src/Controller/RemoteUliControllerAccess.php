<?php

namespace Drupal\multiaccess_uli_ui\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;

/**
 * Allows users to log in to remote sites.
 */
class RemoteUliControllerAccess {

  use DependencyInjectionTrait;

  /**
   * Checks access for a specific request.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account) {
    try {
      $roles = $account->getRoles();

      return AccessResult::allowedIf(count($this->integrationDestinationFactory()->destinationsAvailableToRoles($roles)) > 0);
    }
    catch (\Throwable $t) {
      return AccessResult::forbidden();
    }
  }

}
