<?php

namespace Drupal\multiaccess\User;

/**
 * A user which does not exist.
 */
class MultiAccessUserDoesNotExistException extends \Exception {

  /**
   * Constructor.
   *
   * @param string $email
   *   An email.
   */
  public function __construct(string $email) {
    parent::__construct('A user with the email does not exist: ' . $email);
  }

}
