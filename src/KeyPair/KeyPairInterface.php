<?php

namespace Drupal\multiaccess\KeyPair;

/**
 * Interface for a key pair.
 */
interface KeyPairInterface {

  /**
   * Get the private key.
   *
   * @return string
   *   The private key.
   */
  public function privateKey() : string;

  /**
   * Get the public key.
   *
   * @return string
   *   The public key.
   */
  public function publicKey() : string;

}
