<?php

namespace Drupal\multiaccess\Response;

/**
 * Interface for a response.
 */
interface ResponseInterface {

  /**
   * Whether this response is valid.
   *
   * @return bool
   *   Whether this response is valid.
   */
  public function valid() : bool;

  /**
   * Errors for this response.
   *
   * @return string
   *   Errors for this response.
   */
  public function errors() : string;

  /**
   * Login link.
   *
   * @return string
   *   A login link.
   */
  public function loginLink() : string;

}
