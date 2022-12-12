<?php

namespace Drupal\multiaccess\Integration;

/**
 * Interface for an integration destination. See README.md.
 */
interface IntegrationDestinationInterface extends IntegrationHalfInterface, FormattableIntegrationInterface {

  /**
   * Get the UUID for this integration.
   *
   * @return string
   *   The UUID for this integration.
   */
  public function getIntegrationUuid() : string;

  /**
   * Get the destination URL.
   *
   * @return string
   *   The destination URL.
   */
  public function getUrl() : string;

  /**
   * Get the destination label.
   *
   * @return string
   *   The destination label.
   */
  public function getLabel() : string;

  /**
   * Get a one-time login link.
   *
   * @param string $email
   *   An email.
   *
   * @return string
   *   A one-time login link.
   */
  public function uli(string $email) : string;

  /**
   * Send a ping to test the integration with the destination.
   *
   * @return string
   *   A return message, usually "pong".
   */
  public function ping() : string;

  /**
   * Send an arbitrary message to the destination.
   *
   * @param string $endpoint
   *   An endpoint such as '/api/multiaccess/v1/login-link' which must accept
   *   post requests on the destination.
   * @param array $postParams
   *   Post parameters which are meant to be encrypted.
   *
   * @return array
   *   The reply as an array.
   */
  public function sendMessage(string $endpoint, array $postParams) : array;

}
