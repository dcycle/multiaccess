<?php

namespace Drupal\multiaccess;

use Drupal\multiaccess\Integration\IntegrationInterface;
use Drupal\multiaccess\User\MultiAccessUserInterface;
use Drupal\multiaccess\Utilities\DependencyInjectionTraitInterface;

/**
 * Interface for the MultiAccess app.
 */
interface MultiAccessInterface extends DependencyInjectionTraitInterface {

  /**
   * Create a new local-remote integration.
   *
   * @param string $label
   *   The human-readable label for the destination site.
   * @param string $public
   *   The publicly accessible full URL of the site you want to access, for
   *   example http://site-i-want-to-access.example.com.
   * @param string $internal
   *   The internally accessible full URL of the site you want to access, which
   *   can be different from the public URL if you are using Docker or a
   *   reverse proxy.
   * @param array $role_mapping_array
   *   Keys are roles on the site where you have an account, and values are
   *   each arrays of roles on the site you want to access.
   *
   * @return \Drupal\multiaccess\Integration\IntegrationInterface
   *   An integration object.
   */
  public function newIntegration(string $label, string $public, string $internal, array $role_mapping_array) : IntegrationInterface;

  /**
   * Get the current user.
   *
   * @return \Drupal\multiaccess\User\MultiAccessUserInterface
   *   The current user.
   */
  public function currentUser() : MultiAccessUserInterface;

  /**
   * Get all destinations available to the current user.
   *
   * @return array
   *   Destinations available to the current user.
   */
  public function destinationsForCurrentUser();

  /**
   * Run a self-test on the installations.
   *
   * @param callable $callback
   *   A function whose first parameter is a string which is 'ok' or 'error',
   *   and whose second parameter is a string explaining what was tested.
   *
   * @return bool
   *   TRUE on success, FALSE on failure.
   */
  public function selftest(callable $callback) : bool;

  /**
   * Get user 1's email, useful for testing.
   *
   * @return string
   *   User 1's email.
   */
  public function getUserOneEmail() : string;

  /**
   * Get an existing user, if possible.
   *
   * @param string $email
   *   An email address.
   * @param array $roles
   *   Roles the user must have.
   *
   * @return \Drupal\multiaccess\User\MultiAccessUserInterface
   *   A user or FALSE.
   */
  public function getExistingUser(string $email, array $roles = []) : MultiAccessUserInterface;

  /**
   * Testable implementation of hook_requirements().
   */
  public function hookRequirements(string $phase) : array;

  /**
   * POSTs an HTTP request.
   *
   * @param string $url
   *   An URL.
   * @param array $post
   *   POST params.
   *
   * @return string
   *   The raw result.
   */
  public function httpPost(string $url, array $post) : string;

  /**
   * Get a user, creating it if necessary.
   *
   * @param string $email
   *   The email.
   * @param array $roles
   *   The roles this user should have.
   */
  public function getUser(string $email, array $roles) : MultiAccessUserInterface;

}
