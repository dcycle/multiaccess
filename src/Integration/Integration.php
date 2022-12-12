<?php

namespace Drupal\multiaccess\Integration;

/**
 * An integration between a source and destination site.
 */
class Integration implements IntegrationInterface {

  /**
   * The source.
   *
   * @var \Drupal\multiaccess\Integration\IntegrationSourceInterface
   */
  protected $source;

  /**
   * The destination.
   *
   * @var \Drupal\multiaccess\Integration\IntegrationDestinationInterface
   */
  protected $destination;

  /**
   * Constructor.
   *
   * @param \Drupal\multiaccess\Integration\IntegrationSourceInterface $source
   *   The source.
   * @param \Drupal\multiaccess\Integration\IntegrationDestinationInterface $destination
   *   The destination.
   */
  public function __construct(IntegrationSourceInterface $source, IntegrationDestinationInterface $destination) {
    $this->source = $source;
    $this->destination = $destination;
  }

  /**
   * {@inheritdoc}
   */
  public function format() : string {
    $return = '';
    $return .= $this->destination->format();
    $return .= $this->source->format();
    return $return;
  }

}
