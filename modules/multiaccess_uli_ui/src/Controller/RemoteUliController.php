<?php

namespace Drupal\multiaccess_uli_ui\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\multiaccess\Utilities\CommonUtilitiesTrait;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;

/**
 * Allows users to log in to remote sites.
 */
class RemoteUliController extends ControllerBase {

  use CommonUtilitiesTrait;
  use DependencyInjectionTrait;
  use StringTranslationTrait;

  /**
   * Controller to return a result link, if allowed.
   */
  public function result() {
    $content = [
      '#theme' => 'item_list',
      '#list_type' => 'ul',
      '#title' => $this->t('Remote sites'),
      '#items' => [],
      '#attributes' => ['class' => 'remote-sites'],
      '#wrapper_attributes' => ['class' => 'container'],
    ];

    foreach ($this->app()->destinationsForCurrentUser() as $destination) {
      $title = $this->t('Go to @site', [
        '@site' => $destination->getLabel(),
      ]);

      $content['#items'][] = [
        '#type' => 'link',
        '#title' => $title,
        '#url' => Url::fromRoute('multiaccess_uli_ui.redirect', [
          'uuid' => $destination->getIntegrationUuid(),
        ], [
          'query' => [
            // Technically unnecessery because by default Drupal goes to the
            // user edit page whenever a ULI is used (see for example
            // https://www.drupal.org/project/uli_custom_workflow/issues/3364831),
            // however I like the idea of always using the destination
            // parameter, as our preferred way of generating ULIs.
            'destination' => '/user/edit',
          ],
        ]),
        '#attributes' => [
          'target' => '_blank',
          'title' => $title,
        ],
      ];
    }

    return $content;
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
    try {
      $roles = $account->getRoles();

      return AccessResult::allowedIf(count($this->integrationDestinationFactory()->destinationsAvailableToRoles($roles)) > 0);
    }
    catch (\Throwable $t) {
      $this->logThrowable($t);
      return AccessResult::forbidden();
    }
  }

}
