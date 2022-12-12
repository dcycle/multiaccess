<?php

namespace Drupal\multiaccess\Integration;

use Drupal\multiaccess\RoleMapping\RoleMappingInterface;

/**
 * An integration source.
 */
class IntegrationSource extends IntegrationHalf implements IntegrationSourceInterface {

  /**
   * The role mapping.
   *
   * @var \Drupal\multiaccess\RoleMapping\RoleMappingInterface
   */
  protected $roleMapping;

  /**
   * The destination public url.
   *
   * @var string
   */
  protected $publicUrl;

  /**
   * {@inheritdoc}
   */
  public function fixLoginLinkDomain(string $loginLink) : string {
    return $this->getPublicUrl() . preg_replace('/^[a-z]*:\/\/[^\/]*/', '', $loginLink);
  }

  /**
   * {@inheritdoc}
   */
  public function getPublicUrl() : string {
    return $this->publicUrl;
  }

  /**
   * Constructor.
   *
   * @param string $uuid
   *   The UUID for this integration.
   * @param \Drupal\multiaccess\RoleMapping\RoleMappingInterface $roleMapping
   *   The destination URL.
   * @param string $remotePublicKey
   *   The remote public key.
   * @param string $localPrivateKey
   *   The local private key.
   * @param string $publicUrl
   *   The public url.
   */
  public function __construct(string $uuid, RoleMappingInterface $roleMapping, string $remotePublicKey, string $localPrivateKey, string $publicUrl) {
    $this->roleMapping = $roleMapping;
    $this->publicUrl = $publicUrl;
    parent::__construct(
      uuid: $uuid,
      localPrivateKey: $localPrivateKey,
      remotePublicKey: $remotePublicKey,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function remoteLocation() : string {
    return 'destinations';
  }

  /**
   * {@inheritdoc}
   */
  public function localLocation() : string {
    return 'sources';
  }

  /**
   * {@inheritdoc}
   */
  public function getRoleMapping() : RoleMappingInterface {
    return $this->roleMapping;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsFileContents() {
    return array_merge([
      '["public_url"] = "' . $this->publicUrl . '";',
    ], parent::settingsFileContents(), $this->getRoleMapping()->format());
  }

}
