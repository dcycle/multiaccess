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
   * Get the email for this user.
   *
   * @return string
   *   The email for this user.
   */
  public function email() : string;

  /**
   * Get the ID of this user.
   *
   * @return int
   *   The ID of this user.
   */
  public function id() : int;

  /**
   * Get a user's roles as an array.
   *
   * @return array
   *   Array of roles.
   */
  public function getRoles() : array;

}
