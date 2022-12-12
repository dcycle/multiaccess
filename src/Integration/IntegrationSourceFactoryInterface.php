<?php

namespace Drupal\multiaccess\Integration;

/**
 * Interface for an integration source factory.
 */
interface IntegrationSourceFactoryInterface extends IntegrationFactoryInterface {

  /**
   * Get an integration information from a UUID.
   *
   * @param string $uuid
   *   A uuid.
   *
   * @return \Drupal\multiaccess\Integration\IntegrationSourceInterface
   *   An integration.
   */
  public function fromSourceUuid(string $uuid) : IntegrationSourceInterface;

}
