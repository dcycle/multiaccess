<?php

namespace Drupal\multiaccess\KeyPair;

/**
 * Key pair.
 */
class KeyPair implements KeyPairInterface {

  /**
   * The private key.
   *
   * @var string
   */
  protected $privateKey;


  /**
   * The public key.
   *
   * @var string
   */
  protected $publicKey;

  /**
   * Constructor.
   *
   * @param string $privateKey
   *   The private key.
   * @param string $publicKey
   *   The public key.
   */
  public function __construct(string $privateKey, string $publicKey) {
    $this->privateKey = $privateKey;
    $this->publicKey = $publicKey;
  }

  /**
   * {@inheritdoc}
   */
  public function privateKey() : string {
    return $this->privateKey;
  }

  /**
   * {@inheritdoc}
   */
  public function publicKey() : string {
    return $this->publicKey;
  }

}
