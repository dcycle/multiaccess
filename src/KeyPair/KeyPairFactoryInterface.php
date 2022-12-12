<?php

namespace Drupal\multiaccess\KeyPair;

/**
 * Interface for a key pair utility.
 */
interface KeyPairFactoryInterface {

  /**
   * Get a new key pair.
   *
   * @return \Drupal\multiaccess\KeyPair\KeyPairInterface
   *   A new key pair.
   */
  public function new() : KeyPairInterface;

}
