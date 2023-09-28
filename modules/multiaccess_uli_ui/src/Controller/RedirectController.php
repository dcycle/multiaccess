<?php

namespace Drupal\multiaccess_uli_ui\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\multiaccess\Utilities\CommonUtilitiesTrait;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;
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
    $destination = $this->getDestinationPathOnly($request);

    // https://www.drupal.org/project/drupal/issues/2950883#comment-12790085
    $request->query->remove('destination');

    return $this->getUliAndRedirect($uuid, $destination);
  }

  /**
   * Remove the domain component from a destination.
   *
   * You can call /multiaccess/redirect/DESTINATION?destination=/a/b/c in order
   * to log in to DESTINATION and redirect to /a/b/c on the destination.
   * However, if you are using the
   * [r4032login](http://drupal.org/project/r4032login/releases/2.2.1) module,
   * as described in the README section "Instead of seeing Page not found on
   * the destination, how to automatically log in", which requires you to
   * set, on the destination site, the configuration:
   *
   * drush cset r4032login.settings user_login_path \
   * http://SOURCE/multiaccess/redirect/DESTINATION
   *
   * Then in the source site, we will receive a request which looks like:
   *
   * http%3A//DESTINATION%3APORT/admin/index
   *
   * The destination parameter will be ignored if such is the case.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   A request.
   *
   * @return string
   *   A destination path.
   */
  public function getDestinationPathOnly(Request $request) : string {
    // Let's see if there is a destination path.
    $candidate = $request->query->get('destination') ?: '';

    // If the destination parameter contains
    // http%3A//DESTINATION%3APORT/admin/index, $candidate will be an empty
    // string. We still need the /admin/index part. this might be the case
    // if the destination parameter comes to us from an external site (see
    // method comments).
    if (!$candidate) {
      // The "destination" parameter cannot be named "destination" because if
      // it is, and it contains a destination on an external site (in this case
      // the destination site is external to the source site), then Drupal will
      // not allow us to accss it, even by directly access the $_GET
      // superglobal.
      $candidate = $request->query->get('destination_cannot_be_named_destination') ?: '';
    }

    return $this->removeDomainAndPort($candidate);
  }

  /**
   * Remove the domain name and port from a string.
   *
   * @param string $canContainDomainAndPort
   *   A string such as '', '/admin/index', or
   *   'http%3A//DESTINATION%3APORT/a/b/c.
   *
   * @return string
   *   A string such as '', '/admin/index', or '/a/b/c'.
   */
  public function removeDomainAndPort(string $canContainDomainAndPort) : string {
    return preg_replace('/^[^\/]*\/\/[^\/]*/', '', $canContainDomainAndPort);
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
   * @param string $destinationPath
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
