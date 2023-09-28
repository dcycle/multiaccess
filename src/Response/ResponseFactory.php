<?php

namespace Drupal\multiaccess\Response;

use Drupal\Component\Serialization\Json;
use Drupal\multiaccess\Integration\IntegrationSourceInterface;
use Drupal\multiaccess\Utilities\DependencyInjectionTrait;

/**
 * Response factory.
 */
class ResponseFactory implements ResponseFactoryInterface {

  use DependencyInjectionTrait;

  /**
   * {@inheritdoc}
   */
  public function get(string $email, string $rolesJson, IntegrationSourceInterface $source) : ResponseInterface {
    try {
      $roles = $source->getRoleMapping()
        ->sourceToDestinationRoles(Json::decode($rolesJson));
      return new ValidResponse($this->app()
        ->getUser($email, $roles)
        ->getLoginLink());
    }
    catch (\Throwable $t) {
      return new ThrowableResponse($t);
    }
  }

}
