<?php

namespace Drupal\multiaccess\Integration;

use Drupal\Component\Serialization\Json;

/**
 * An integration destination interface.
 */
class IntegrationDestination extends IntegrationHalf implements IntegrationDestinationInterface {

  /**
   * The destination URL.
   *
   * @var string
   */
  protected $url;

  /**
   * The destination human-readable label.
   *
   * @var string
   */
  protected $label;

  /**
   * Constructor.
   *
   * @param string $uuid
   *   The UUID for this integration.
   * @param string $url
   *   The destination URL.
   * @param string $remotePublicKey
   *   The remote public key.
   * @param string $localPrivateKey
   *   The local private key.
   * @param string $label
   *   The human-readable label of this destination.
   */
  public function __construct(string $uuid, string $url, string $remotePublicKey, string $localPrivateKey, string $label) {
    if (!$url) {
      throw new \Exception('Url cannot be empty.');
    }

    if (!$label) {
      throw new \Exception('Label cannot be empty.');
    }

    $this->url = $url;
    $this->label = $label;
    parent::__construct(
      uuid: $uuid,
      localPrivateKey: $localPrivateKey,
      remotePublicKey: $remotePublicKey,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function ping() : string {
    $decodedResult = $this->sendMessage('/api/multiaccess/v1/ping', [
      'ping' => 'ping',
    ]);

    if (!empty($decodedResult['error'])) {
      throw new \Exception($decodedResult['error']);
    }

    return $this->decrypt($decodedResult['pong']);
  }

  /**
   * {@inheritdoc}
   */
  public function sendMessage(string $endpoint, array $postParams) : array {
    $url = $this->getUrl();

    array_walk($postParams, function ($item, $key) use (&$postParams) {
      $postParams[$key] = $this->encrypt($item);
    });

    $result = $this->app()->httpPost($url . $endpoint, array_merge($postParams, [
      'source' => $this->getUuid(),
    ]));

    $decodedResult = Json::decode($result);

    if (!is_array($decodedResult)) {
      throw new \Exception('Decrypted result is not an array');
    }

    if (!empty($decodedResult['error'])) {
      throw new \Exception($decodedResult['error']);
    }

    return $decodedResult;
  }

  /**
   * {@inheritdoc}
   */
  public function uli(string $email) : string {
    $existing_user = $this->app()->getExistingUser($email);

    $decodedResult = $this->sendMessage('/api/multiaccess/v1/login-link', [
      'encrpyted_email' => $email,
      'encrpyted_roles' => Json::encode($existing_user->getRoles()),
    ]);

    if (!empty($decodedResult['error'])) {
      throw new \Exception($decodedResult['error']);
    }

    return $this->decrypt($decodedResult['link']);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsFileContents() {
    return array_merge([
      '["remote_url"] = "' . $this->getUrl() . '";',
      '["label"] = "' . $this->label . '";',
    ], parent::settingsFileContents());
  }

  /**
   * {@inheritdoc}
   */
  public function getUrl() : string {
    $candidate = $this->url;

    if (!$candidate) {
      throw new \Exception('url cannot be empty');
    }

    return $candidate;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() : string {
    $candidate = $this->label;

    if (!$candidate) {
      throw new \Exception('label cannot be empty');
    }

    return $candidate;
  }

  /**
   * {@inheritdoc}
   */
  public function remoteLocation() : string {
    return 'sources';
  }

  /**
   * {@inheritdoc}
   */
  public function localLocation() : string {
    return 'destinations';
  }

}
