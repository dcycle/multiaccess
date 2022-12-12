<?php

namespace Drupal\multiaccess\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Controller to get a login link.
 */
class MultiAccessLoginLink extends MultiAccessControllerBase {

  /**
   * {@inheritdoc}
   */
  public function getResultData(Request $request) : array {
    $email = $this->decryptedPostVal($request, 'encrpyted_email');
    $roles = $this->decryptedPostVal($request, 'encrpyted_roles');

    $response = $this->responseFactory()
      ->get($email, $roles, $this->integrationSource($request));

    if ($response->valid()) {
      $integrationSource = $this->integrationSource($request);

      $loginLinkWithWrongDomain = $response->loginLink();

      $loginLink = $integrationSource
        ->fixLoginLinkDomain($loginLinkWithWrongDomain);

      $return['link'] = $this->encrypt($request, $loginLink);
    }
    else {
      $return['error'] = $response->errors();
    }

    return $return;
  }

}
