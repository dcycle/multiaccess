<?php

namespace Drupal\multiaccess\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;
use Drupal\multiaccess\Integration\IntegrationSourceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Controller to get a pong for a ping.
 */
abstract class MultiAccessControllerBase extends ControllerBase {

  use DependencyInjectionTrait;

  /**
   * Controller to return a result link, if allowed.
   */
  public function getResult(Request $request) {
    try {
      return new JsonResponse($this->getResultData($request));
    }
    catch (\Throwable $t) {
      $return = new JsonResponse([
        'error' => $t->getMessage(),
      ]);
      $return->setStatusCode(500);
      return $return;
    }
  }

  /**
   * Return data for this controller as array, if allowed.
   */
  abstract public function getResultData(Request $request) : array;

  /**
   * Get a required POST parameter.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param string $name
   *   The POST parameter name.
   *
   * @return string
   *   The value which cannot be empty.
   */
  public function postVal(Request $request, string $name) : string {
    $candidate = $request->request->get($name);

    if (!$candidate) {
      throw new \Exception('POST param ' . $name . ' cannot be empty.');
    }

    return $candidate;
  }

  /**
   * Get a required POST parameter and decrypt it.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param string $name
   *   The POST parameter name.
   *
   * @return string
   *   The decrypted value which cannot be empty.
   */
  public function decryptedPostVal(Request $request, string $name) : string {
    return $this->decrypt($request, $this->postVal($request, $name));
  }

  /**
   * Encrypt a string.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param string $unencrypted
   *   An unencrypted string.
   *
   * @return string
   *   An encrypted string.
   */
  public function encrypt(Request $request, string $unencrypted) : string {
    return $this->integrationSource($request)->encrypt($unencrypted);
  }

  /**
   * Decrypt a string.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   * @param string $encrypted
   *   An encrypted string.
   *
   * @return string
   *   A decrypted string.
   */
  public function decrypt(Request $request, string $encrypted) : string {
    return $this->integrationSource($request)->decrypt($encrypted);
  }

  /**
   * Get the integration source associated with this request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Drupal\multiaccess\Integration\IntegrationSourceInterface
   *   The integration source.
   */
  public function integrationSource(Request $request) : IntegrationSourceInterface {
    return $this->integrationSourceFactory()
      ->fromSourceUuid($this->postVal($request, 'source'));
  }

}
