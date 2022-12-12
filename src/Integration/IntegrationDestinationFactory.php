<?php

namespace Drupal\multiaccess\Integration;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;

/**
 * Creator of access info objects.
 */
class IntegrationDestinationFactory extends IntegrationFactory implements IntegrationDestinationFactoryInterface {

  use StringTranslationTrait;
  use DependencyInjectionTrait;

  /**
   * {@inheritdoc}
   */
  public function configKey() : string {
    return 'destinations';
  }

  /**
   * {@inheritdoc}
   */
  public function firstDestination() : IntegrationDestinationInterface {
    $candidate = $this->first();

    if ($candidate instanceof IntegrationDestinationInterface) {
      return $candidate;
    }

    throw new \Exception('Internal error: HalfIntegration not destination ingration as expected.');
  }

  /**
   * {@inheritdoc}
   */
  public function fromSettingsLine(string $key, array $line) : IntegrationDestinationInterface {
    return new IntegrationDestination(
      uuid: $key,
      url: $line['remote_url'] ?? '',
      localPrivateKey: $line['local_private_key'] ?? '',
      remotePublicKey: $line['remote_public_key'] ?? '',
      label: $line['label'] ?? '',
      accessibleToRoles: $line['accessible_to_roles'] ?? [],
    );
  }

  /**
   * {@inheritdoc}
   */
  public function destinationsAvailableToRoles(array $roles) {
    $return = [];

    $destinations = $this->allFromUnversionedSettingsFile();

    foreach ($destinations as $destination) {
      if ($destination->availableToRolesAmong($roles)) {
        $return[] = $destination;
      }
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function fromDestinationUuid(string $uuid) : IntegrationDestinationInterface {
    $candidate = parent::fromUuid($uuid);

    if ($candidate instanceof IntegrationDestinationInterface) {
      return $candidate;
    }

    throw new \Exception('Internal error: HalfIntegration not destination ingration as expected.');
  }

}
