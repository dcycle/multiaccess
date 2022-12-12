<?php

namespace Drupal\multiaccess\Response;

/**
 * A response which results from a \Throwable.
 */
class ThrowableResponse implements ResponseInterface {

  /**
   * The \Throwable.
   *
   * @var \Throwable
   */
  protected $t;

  /**
   * Constructor.
   *
   * @param \Throwable $t
   *   The \Throwable.
   */
  public function __construct(\Throwable $t) {
    $this->t = $t;
  }

  /**
   * {@inheritdoc}
   */
  public function valid() : bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function errors() : string {
    return $this->t->getMessage() . ' on ' . $this->t->getFile() . ':' . $this->t->getLine();
  }

  /**
   * {@inheritdoc}
   */
  public function loginLink() : string {
    throw new \Exception('Cannot generate a login link.');
  }

}
