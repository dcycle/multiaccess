<?php

namespace Drupal\multiaccess\Utilities;

use Drupal\multiaccess\MultiAccessInterface;
use Drupal\multiaccess\Integration\IntegrationSourceFactoryInterface;
use Drupal\multiaccess\Integration\IntegrationDestinationFactoryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\multiaccess\Response\ResponseFactoryInterface;
use Drupal\multiaccess\RoleMapping\RoleMappingFactoryInterface;
use Drupal\multiaccess\KeyPair\KeyPairFactoryInterface;

/**
 * Interface for dependency injection.
 */
interface DependencyInjectionTraitInterface {

  /**
   * Mockable wrapper for \Drupal::service('email.validator').
   */
  public function emailValidator();

  /**
   * Helper function to set a message.
   */
  public function drupalSetMessage(string $message);

  /**
   * Mockable wrapper for \Drupal::messenger().
   */
  public function messenger();

  /**
   * Wrapper around \Drupal::httpClient().
   */
  public function httpClient();

  /**
   * Mockable wrapper for \Drupal::service('multiaccess').
   */
  public function app() : MultiAccessInterface;

  /**
   * Mockable wrapper for \Drupal::service('multiaccess.role_mapping_factory').
   */
  public function roleMappingFactory() : RoleMappingFactoryInterface;

  /**
   * Mockable wrapper for \Drupal::time().
   */
  public function time();

  /**
   * Mockable wrapper for \Drupal::logger(...).
   */
  public function drupalLogger(string $channel);

  /**
   * Mockable wrapper for \Drupal::service('uuid').
   */
  public function uuid();

  /**
   * Mockable wrapper around \Drupal::currentUser().
   */
  public function currentDrupalUserObject();

  /**
   * Mockable wrapper around \Drupal::service('multiaccess.response_factory').
   */
  public function responseFactory() : ResponseFactoryInterface;

  /**
   * Mockable wrapper around \Drupal::service('entity_type.manager').
   */
  public function entityTypeManager();

  /**
   * Wrapper around \Drupal::service('multiaccess.access_info_factory').
   */
  public function integrationSourceFactory() : IntegrationSourceFactoryInterface;

  /**
   * Wrapper around \Drupal::service('multiaccess.key_pair_factory').
   */
  public function keyPairFactory() : KeyPairFactoryInterface;

  /**
   * Wrapper around \Drupal::service('multiaccess.access_info_factory').
   */
  public function integrationDestinationFactory() : IntegrationDestinationFactoryInterface;

  /**
   * Mockable wrapper around \Drupal::service('config.factory').
   */
  public function configFactory() : ConfigFactoryInterface;

}
