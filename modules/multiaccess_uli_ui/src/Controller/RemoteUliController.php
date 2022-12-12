<?php

namespace Drupal\multiaccess_uli_ui\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;

/**
 * Allows users to log in to remote sites.
 */
class RemoteUliController {

  use DependencyInjectionTrait;

  /**
   * Returns a page to access remote unique login links.
   *
   * @return array
   *   A renderable array.
   */
  public function get() {
    return [
      '#markup' => 'Hello, world',
    ];
  }

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
    $roles = $account->getRoles();

    return AccessResult::allowedIf(count($this->destinationsAvailableToRoles($roles)) > 0);
  }

  /**
   * Get destinations available for given roles.
   *
   * @param array $roles
   *   Role ids.
   *
   * @return array
   *   Destinations.
   */
  public function destinationsAvailableToRoles(array $roles) {
    $return = [];

    $destinations = $this->integrationDestinationFactory()
      ->allFromUnversionedSettingsFile();

    foreach ($destinations as $destination) {
      if ($destination->availableToRolesAmong($roles)) {
        $return[] = $destination;
      }
    }

    return $return;
  }

}
