<?php

namespace Drupal\multiaccess\Response;

/**
 * A response which is valid.
 */
class ValidResponse implements ResponseInterface {

  /**
   * The unencrypted response.
   *
   * @var string
   */
  protected $unencrypted;

  /**
   * Constructor.
   *
   * @param string $unencrypted
   *   The unencrypted response.
   */
  public function __construct(string $unencrypted) {
    $this->unencrypted = $unencrypted;
  }

  /**
   * {@inheritdoc}
   */
  public function valid() : bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function errors() : string {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function loginLink() : string {
    return $this->unencrypted;
  }

}
