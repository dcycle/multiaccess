<?php

namespace Drupal\Tests\multiaccess\Integration\Unit;

use Drupal\multiaccess\Integration\IntegrationHalf;
use PHPUnit\Framework\TestCase;

/**
 * Test IntegrationHalf.
 *
 * @group multiaccess
 */
class IntegrationHalfTest extends TestCase {

  /**
   * Smoke test.
   */
  public function testSmoke() {
    $object = $this->getMockBuilder(IntegrationHalf::class)
      // NULL = no methods are mocked; otherwise list the methods here.
      ->setMethods(NULL)
      ->disableOriginalConstructor()
      ->getMockForAbstractClass();

    $this->assertTrue(is_object($object));
  }

}
