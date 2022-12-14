<?php

namespace Drupal\multiaccess\Integration;

use Drupal\multiaccess\Utilities\DependencyInjectionTrait;
use Drupal\multiaccess\Utilities\CommonUtilitiesTrait;

/**
 * Half of an integration.
 */
abstract class IntegrationHalf implements FormattableIntegrationInterface, IntegrationHalfInterface {

  use DependencyInjectionTrait;
  use CommonUtilitiesTrait;

  /**
   * The remote public key.
   *
   * @var string
   */
  protected $remotePublicKey;

  /**
   * The local private key.
   *
   * @var string
   */
  protected $localPrivateKey;

  /**
   * The UUID.
   *
   * @var string
   */
  protected $uuid;

  /**
   * Constructor.
   *
   * @param string $uuid
   *   The integration uuid.
   * @param string $remotePublicKey
   *   The remote public key.
   * @param string $localPrivateKey
   *   The local private key.
   */
  public function __construct(string $uuid, string $remotePublicKey, string $localPrivateKey) {
    if (!$uuid) {
      throw new \Exception('uuid cannot be empty.');
    }
    if (!$remotePublicKey) {
      throw new \Exception('remotePublicKey cannot be empty.');
    }
    if (!$localPrivateKey) {
      throw new \Exception('localPrivateKey cannot be empty.');
    }

    $this->uuid = $uuid;
    $this->remotePublicKey = $remotePublicKey;
    $this->localPrivateKey = $localPrivateKey;
  }

  /**
   * {@inheritdoc}
   */
  public function getUuid() : string {
    return $this->uuid;
  }

  /**
   * {@inheritdoc}
   */
  public function format() : string {
    $return = '';
    $return .= '/**' . PHP_EOL;
    $return .= ' * MultiAccess for Drupal integration information.' . PHP_EOL;
    $return .= ' *' . PHP_EOL;
    $return .= ' * See http://drupal.org/project/multiaccess.' . PHP_EOL;
    $return .= ' *' . PHP_EOL;
    $return .= ' * ***' . PHP_EOL;
    $return .= ' *' . PHP_EOL;
    $return .= ' * Put the following ONLY in your ' . $this->localLocation() . ' website unversioned' . PHP_EOL;
    $return .= ' * settings.php file.' . PHP_EOL;
    $return .= ' *' . PHP_EOL;
    $return .= ' * SECURITY RISK: .' . PHP_EOL;
    $return .= ' * * Do not put the following in the ' . $this->remoteLocation() . ' website.' . PHP_EOL;
    $return .= ' * * Do not put this in version control.' . PHP_EOL;
    $return .= ' * * Do not use the Drupal config management system for this info.' . PHP_EOL;
    $return .= ' */' . PHP_EOL;
    foreach ($this->settingsFileContents() as $line) {
      $return .= '$config["multiaccess.unversioned"]["' . $this->remoteLocation() . '"]["' . $this->getIntegrationUuid() . '"]' . $line . PHP_EOL;
    }
    $return .= '/**' . PHP_EOL;
    $return .= ' * MultiAccess end ' . $this->localLocation() . ' unversioned config.' . PHP_EOL;
    $return .= ' */' . PHP_EOL;
    return $return;
  }

  /**
   * Return the contents of the settings file.
   *
   * @return array
   *   The contents of the settings file as an array.
   */
  public function settingsFileContents() {
    return [
      '["remote_public_key"] = "' . $this->getRemotePublicKey() . '";',
      '["local_private_key"] = "' . $this->getLocalPrivateKey() . '";',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getIntegrationUuid() : string {
    return $this->uuid;
  }

  /**
   * Return the remote location.
   *
   * @return string
   *   Remote location.
   */
  abstract public function remoteLocation() : string;

  /**
   * Return the local location.
   *
   * @return string
   *   Local location.
   */
  abstract public function localLocation() : string;

  /**
   * {@inheritdoc}
   */
  public function getRemotePublicKey() : string {
    return $this->remotePublicKey;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocalPrivateKey() : string {
    return $this->localPrivateKey;
  }

  /**
   * {@inheritdoc}
   */
  public function encrypt(string $decrypted) : string {
    if (!$decrypted) {
      throw new \Exception('Decrypetd string to be encrypted cannot be empty');
    }
    $encrypted = '';
    $public_key = $this->getRemotePublicKey();
    if (!openssl_public_encrypt($decrypted, $encrypted, $public_key)) {
      throw new \Exception('Could not encrypt the data with public key');
    };
    $candidate = base64_encode($encrypted);
    if (!$candidate) {
      throw new \Exception('Encrypted string cannot be empty');
    }
    return $candidate;
  }

  /**
   * {@inheritdoc}
   */
  public function decrypt(string $encrypted) : string {
    if (!$encrypted) {
      throw new \Exception('Encrypted string to be decrypted cannot be empty');
    }
    $decrypted = '';
    if (!openssl_private_decrypt(base64_decode($encrypted), $decrypted, $this->getLocalPrivateKey())) {
      throw new \Exception('Could not decrypt the data passed by ' . $this->caller() . '. This happens if the openssl_private_decrypt() returns FALSE. We know the local private key is not empty and that the data to decrypt is not empty. The data might have been encrypted with the wrong public key, or you might be passing data which is not really encrypted.');
    }
    $candidate = $decrypted;
    if (!$candidate) {
      throw new \Exception('Decrypted string cannot be empty');
    }
    return $candidate;
  }

}
