<?php

namespace Drupal\multiaccess\Utilities;

use Drupal\Core\Utility\Error;

/**
 * Common utilities which may be useful to any class.
 */
trait CommonUtilitiesTrait {

  use DependencyInjectionTrait;

  /**
   * Get the calling function.
   *
   * @return string
   *   The calling function.
   */
  public function caller() : string {
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
    return $backtrace['file'] . ':' . $backtrace['line'];
  }

  /**
   * Log a \Throwable.
   */
  public function logThrowable(\Throwable $t) {
    $variables = Error::decodeException($t);
    $this->drupalLogger('multiaccess')->error('%type: @message in %function (line %line of %file).', $variables);
  }

  /**
   * Return a \Throwable as a string.
   *
   * @param \Throwable $t
   *   A \Throwable.
   *
   * @return string
   *   The \Throwable as a string.
   */
  public function throwableToString(\Throwable $t) : string {
    return $t->getMessage() . ' ' . $t->getFile() . ':' . $t->getLine();
  }

}
