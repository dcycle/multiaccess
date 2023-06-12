<?php

namespace Drupal\multiaccess_uli_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\multiaccess\Utilities\CommonUtilitiesTrait;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Symfony\Component\HttpFoundation\Request;

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
  public function result(string $uuid, Request $request) {
    return $this->getUliAndRedirect($uuid, $request->query->get('destination') ?: '');
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
   * Redirect to the uli on the external site.
   *
   * @param string $uuid
   *   A destination UUID.
   * @param string $destination
   *   A destination path on the destination site.
   */
  public function getUliAndRedirect(string $uuid, string $destinationPath) {
    try {
      foreach ($this->app()->destinationsForCurrentUser() as $destination) {
        if ($destination->getIntegrationUuid() == $uuid) {
          $uli = $destination->uli($this->app()->currentUser()->email(), $destinationPath);
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
