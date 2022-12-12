<?php

namespace Drupal\multiaccess\Response;

use Drupal\multiaccess\Integration\IntegrationSourceInterface;

/**
 * Interface for a response factory.
 */
interface ResponseFactoryInterface {

  /**
   * Get a response for a source site an email.
   *
   * @param string $email
   *   An decrpyted account email.
   * @param string $rolesJson
   *   Decrypted account roles in JSON format.
   * @param \Drupal\multiaccess\Integration\IntegrationSourceInterface $source
   *   The integration source.
   *
   * @return \Drupal\multiaccess\Response\ResponseInterface
   *   A response.
   */
  public function get(string $email, string $rolesJson, IntegrationSourceInterface $source) : ResponseInterface;

}
