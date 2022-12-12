<?php

namespace Drupal\multiaccess\Integration;

/**
 * Interface for an integration factory.
 */
interface IntegrationFactoryInterface {

  /**
   * Get the first integration.
   *
   * @return \Drupal\multiaccess\Integration\IntegrationHalfInterface
   *   The first integration.
   */
  public function first() : IntegrationHalfInterface;

  /**
   * Get all access info from unversioned settings file.
   *
   * @return array
   *   All integrations from the unversioned settings files.
   */
  public function allFromUnversionedSettingsFile() : array;

  /**
   * Get an integration information from a UUID.
   *
   * @param string $uuid
   *   A uuid.
   *
   * @return \Drupal\multiaccess\Integration\IntegrationHalfInterface
   *   An integration.
   */
  public function fromUuid(string $uuid) : IntegrationHalfInterface;

}
