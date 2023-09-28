<?php

namespace Drupal\multiaccess\Utilities;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\multiaccess\Integration\IntegrationDestinationFactoryInterface;
use Drupal\multiaccess\Integration\IntegrationSourceFactoryInterface;
use Drupal\multiaccess\KeyPair\KeyPairFactoryInterface;
use Drupal\multiaccess\MultiAccessInterface;
use Drupal\multiaccess\Response\ResponseFactoryInterface;
use Drupal\multiaccess\RoleMapping\RoleMappingFactoryInterface;

/**
 * Dependency injection as a trait.
 */
trait DependencyInjectionTrait {

  /**
   * Mockable wrapper for \Drupal::service('email.validator').
   */
  public function emailValidator() {
    return \Drupal::service('email.validator');
  }

  /**
   * {@inheritdoc}
   */
  public function drupalSetMessage(string $message) {
    $this->messenger()->addMessage($message);
  }

  /**
   * {@inheritdoc}
   */
  public function messenger() {
    return \Drupal::messenger();
  }

  /**
   * {@inheritdoc}
   */
  public function currentDrupalUserObject() {
    return \Drupal::currentUser();
  }

  /**
   * Mockable wrapper for \Drupal::entityQuery($type).
   */
  public function entityQuery(string $type) {
    return \Drupal::entityQuery($type);
  }

  /**
   * {@inheritdoc}
   */
  public function time() {
    return \Drupal::time();
  }

  /**
   * {@inheritdoc}
   */
  public function drupalLogger(string $channel) {
    return \Drupal::logger($channel);
  }

  /**
   * Mockable wrapper for \Drupal::service('multiaccess').
   */
  public function app() : MultiAccessInterface {
    return \Drupal::service('multiaccess');
  }

  /**
   * Mockable wrapper for \Drupal::service('multiaccess.role_mapping_factory').
   */
  public function roleMappingFactory() : RoleMappingFactoryInterface {
    return \Drupal::service('multiaccess.role_mapping_factory');
  }

  /**
   * Mockable wrapper for \Drupal::service('uuid').
   */
  public function uuid() {
    return \Drupal::service('uuid');
  }

  /**
   * Mockable wrapper around \Drupal::service('multiaccess.response_factory').
   */
  public function responseFactory() : ResponseFactoryInterface {
    return \Drupal::service('multiaccess.response_factory');
  }

  /**
   * Mockable wrapper around \Drupal::service('entity_type.manager').
   */
  public function entityTypeManager() {
    return \Drupal::service('entity_type.manager');
  }

  /**
   * Wrapper around \Drupal::service('multiaccess.access_info_factory').
   */
  public function integrationSourceFactory() : IntegrationSourceFactoryInterface {
    return \Drupal::service('multiaccess.integration_source_factory');
  }

  /**
   * Wrapper around \Drupal::service('multiaccess.key_pair_factory').
   */
  public function keyPairFactory() : KeyPairFactoryInterface {
    return \Drupal::service('multiaccess.key_pair_factory');
  }

  /**
   * Wrapper around \Drupal::httpClient().
   */
  public function httpClient() {
    return \Drupal::httpClient();
  }

  /**
   * Wrapper around \Drupal::service('multiaccess.access_info_factory').
   */
  public function integrationDestinationFactory() : IntegrationDestinationFactoryInterface {
    return \Drupal::service('multiaccess.integration_destination_factory');
  }

  /**
   * Mockable wrapper around \Drupal::service('config.factory').
   */
  public function configFactory() : ConfigFactoryInterface {
    return \Drupal::service('config.factory');
  }

}
