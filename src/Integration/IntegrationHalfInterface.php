<?php

namespace Drupal\multiaccess\Integration;

/**
 * Interface for half an integration.
 */
interface IntegrationHalfInterface {

  /**
   * Return the remote public key.
   *
   * @return string
   *   The remote public key.
   */
  public function getRemotePublicKey() : string;

  /**
   * Return the local private key.
   *
   * @return string
   *   The local private key.
   */
  public function getLocalPrivateKey() : string;

  /**
   * Get the UUID for this integration.
   *
   * @return string
   *   The UUID for this integration.
   */
  public function getUuid() : string;

  /**
   * Encrypt a string.
   *
   * @param string $decrypted
   *   The decrypted string.
   *
   * @return string
   *   The encrypted string.
   */
  public function encrypt(string $decrypted) : string;

  /**
   * Decrypt a string.
   *
   * @param string $encrypted
   *   The encrypted string.
   *
   * @return string
   *   The decrypted string.
   */
  public function decrypt(string $encrypted) : string;

}
