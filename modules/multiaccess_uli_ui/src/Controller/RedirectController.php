<?php

namespace Drupal\multiaccess_uli_ui\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\multiaccess\Utilities\CommonUtilitiesTrait;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;

/**
 * Allows users to log in to remote sites.
 */
class RedirectController extends ControllerBase {

  use CommonUtilitiesTrait;
  use DependencyInjectionTrait;
  use StringTranslationTrait;

  /**
   * Controller to return a result link, if allowed.
   */
  public function result(string $uuid, string $timestamp, string $token) {
    if ($this->validToken($timestamp, $token)) {
      return $this->getUliAndRedirect($uuid);
    }
    $this->drupalSetMessage($this->t('This page has become out of date, please try again.'));
    return $this->redirectToAllResults();
  }

  /**
   * Get a redirect response to all results.
   */
  public function redirectToAllResults() {
    return $this->redirect('multiaccess_uli_ui.user.remote', [
      'user' => $this->app()->currentUser()->id(),
    ]);
  }

  /**
   * Check if the security token is valid.
   *
   * @param string $timestamp
   *   A timestamp when the token was generated.
   * @param string $token
   *   A token.
   *
   * @return bool
   *   TRUE if the token is valid.
   */
  public function validToken(string $timestamp, string $token) : bool {
    $now = $this->time()->getRequestTime();
    if ($now > intval($timestamp) + 24 * 60 * 60) {
      return FALSE;
    }
    return $token == $this->app()->currentUser()->securityToken(intval($timestamp));
  }

  /**
   * Redirect to the uli on the external site.
   *
   * @param string $uuid
   *   A destination UUID.
   */
  public function getUliAndRedirect(string $uuid) {
    try {
      foreach ($this->app()->destinationsForCurrentUser() as $destination) {
        if ($destination->getIntegrationUuid() == $uuid) {
          $uli = $destination->uli($this->app()->currentUser()->email());
          return new TrustedRedirectResponse($uli);
        }
      }
      throw new \Exception('The current user does not have access to the site ' . $uuid);
    }
    catch (\Throwable $t) {
      $this->logThrowable($t);
      $this->drupalSetMessage($this->t('Could not redirect you because @m.', [
        '@m' => $t->getMessage(),
      ]));
      return $this->redirectToAllResults();
    }
  }

}
