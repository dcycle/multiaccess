<?php

namespace Drupal\multiaccess\Integration;

/**
 * Interface for an integration destination factory.
 */
interface IntegrationDestinationFactoryInterface extends IntegrationFactoryInterface {

  /**
   * Get the first integration.
   *
   * @return \Drupal\multiaccess\Integration\IntegrationDestinationInterface
   *   The first integration.
   */
  public function firstDestination() : IntegrationDestinationInterface;

  /**
   * Get an integration information from a UUID.
   *
   * @param string $uuid
   *   A uuid.
   *
   * @return \Drupal\multiaccess\Integration\IntegrationDestinationInterface
   *   An integration.
   */
  public function fromDestinationUuid(string $uuid) : IntegrationDestinationInterface;

  /**
   * Get all destinations available to one of these roles.
   *
   * @param array $roles
   *   Array of roles.
   *
   * @return array
   *   Array of destinations.
   */
  public function destinationsAvailableToRoles(array $roles);

}
