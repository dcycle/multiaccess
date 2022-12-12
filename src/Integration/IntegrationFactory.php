<?php

namespace Drupal\multiaccess\Integration;

use Drupal\multiaccess\Utilities\DependencyInjectionTrait;

/**
 * Creator of access info objects.
 */
abstract class IntegrationFactory implements IntegrationFactoryInterface {

  use DependencyInjectionTrait;

  /**
   * {@inheritdoc}
   */
  public function first() : IntegrationHalfInterface {
    $all = $this->allFromUnversionedSettingsFile();

    if (count($all)) {
      return reset($all);
    }

    throw new \Exception('Cannot get first integration half before there are no integrations.');
  }

  /**
   * {@inheritdoc}
   */
  public function allFromUnversionedSettingsFile() : array {
    $return = [];

    $candidate = $this->configFactory()
      ->get('multiaccess.unversioned')
      ->get($this->configKey()) ?: [];

    foreach ($candidate as $key => $line) {
      $return[$key] = $this->fromSettingsLine($key, $line);
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public function fromUuid(string $uuid) : IntegrationHalfInterface {
    if (!$uuid) {
      throw new \Exception('Uuid cannot be empty');
    }

    $all = $this->allFromUnversionedSettingsFile();

    if (empty($all[$uuid])) {
      throw new \Exception($this->configKey() . ' ' . $uuid . ' does not exist. There are ' . count($all) . ' integration(s)' . (count($all) ? '. Try: ' . implode(', ', array_keys($all)) : ''));
    }

    return $all[$uuid];
  }

  /**
   * Get the config key, source or destination.
   *
   * @return string
   *   The config key, source or destination.
   */
  abstract public function configKey() : string;

  /**
   * Get an integration from a line in unversioned settings.
   *
   * @param string $key
   *   A line key in settings.
   * @param array $line
   *   A line in settings.
   *
   * @return \Drupal\multiaccess\Integration\IntegrationHalfInterface
   *   An integration.
   */
  abstract public function fromSettingsLine(string $key, array $line) : IntegrationHalfInterface;

}
