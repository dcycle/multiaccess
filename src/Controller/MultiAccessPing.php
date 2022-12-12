<?php

namespace Drupal\multiaccess\Controller;

use Symfony\Component\HttpFoundation\Request;

/**
 * Controller to get a pong for a ping.
 */
class MultiAccessPing extends MultiAccessControllerBase {

  /**
   * {@inheritdoc}
   */
  public function getResultData(Request $request) : array {
    $ping = $this->decryptedPostVal($request, 'ping');

    if ($ping !== 'ping') {
      throw new \Exception('Value of "ping" param is not "ping", it is ' . $ping);
    }

    $return['pong'] = $this->encrypt($request, 'pong');

    return $return;
  }

}
