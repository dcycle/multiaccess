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
   * @param string $destinationPath
   *   A destination path or an empty string for the default behaviour of
   *   going to the user profile edit page.
   *
   * @return string
   *   A one-time login link.
   */
  public function uli(string $email, string $destinationPath = '') : string;

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

  /**
   * Check whether this destination is available to one of these roles.
   *
   * If a destination is available to no roles, then it can still be accessed
   * programmatically, just not by specific users. This method is a helper
   * to modules such as multiaccess_uli_ui, which control access to
   * specific users. If you only use multiaccess programmatically, the concept
   * of role access is moot.
   *
   * @param array $roles
   *   Array of roles.
   *
   * @return bool
   *   Whether one of these roles can access this destination.
   */
  public function availableToRolesAmong(array $roles) : bool;

}
