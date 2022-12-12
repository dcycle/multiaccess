<?php

namespace Drupal\multiaccess\Utilities;

/**
 * Common utilities which may be useful to any class.
 */
trait CommonUtilitiesTrait {

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

}
