<?php

namespace Drupal\multiaccess\Integration;

/**
 * An integration collection.
 */
class IntegrationCollection {

  /**
   * The integrations.
   *
   * @var array
   */
  protected $integrations;

  /**
   * Constructor.
   *
   * @param array $integrations
   *   The integrations.
   */
  public function __construct(array $integrations) {
    $this->integrations = $integrations;
  }

}
