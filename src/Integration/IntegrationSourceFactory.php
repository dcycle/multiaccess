<?php

namespace Drupal\multiaccess\Integration;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;
use Drupal\multiaccess\RoleMapping\RoleMapping;

/**
 * Creator of access info objects.
 */
class IntegrationSourceFactory extends IntegrationFactory implements IntegrationSourceFactoryInterface {

  use StringTranslationTrait;
  use DependencyInjectionTrait;

  /**
   * {@inheritdoc}
   */
  public function configKey() : string {
    return 'sources';
  }

  /**
   * {@inheritdoc}
   */
  public function fromSettingsLine(string $key, array $line) : IntegrationSourceInterface {
    return new IntegrationSource(
      uuid: $key,
      roleMapping: new RoleMapping($line['role_mapping']),
      localPrivateKey: $line['local_private_key'] ?? '',
      remotePublicKey: $line['remote_public_key'] ?? '',
      publicUrl: $line['public_url'] ?? '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function fromSourceUuid(string $uuid) : IntegrationSourceInterface {
    $candidate = parent::fromUuid($uuid);

    if ($candidate instanceof IntegrationSourceInterface) {
      return $candidate;
    }

    throw new \Exception('Internal error: HalfIntegration not source ingration as expected.');
  }

}
