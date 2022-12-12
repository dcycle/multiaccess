<?php

namespace Drupal\multiaccess\Integration;

/**
 * An integration part which can be formatted.
 */
interface FormattableIntegrationInterface {

  /**
   * Format to put into unversioned settings file.
   *
   * @return string
   *   The strings to put into the unversioned settings file.
   */
  public function format() : string;

}
