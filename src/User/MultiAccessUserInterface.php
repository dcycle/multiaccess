<?php

namespace Drupal\multiaccess\User;

/**
 * Interface for a user.
 */
interface MultiAccessUserInterface {

  /**
   * Get a login link for this user.
   *
   * @return string
   *   A login path, excluding the URL.
   */
  public function getLoginLink() : string;

  /**
   * Get a user's roles as an array.
   *
   * @return array
   *   Array of roles.
   */
  public function getRoles() : array;

}
