<?php

namespace Drupal\Tests\multiaccess\Controller\Unit;

use Drupal\multiaccess\Controller\MultiAccessLoginLink;
use PHPUnit\Framework\TestCase;

/**
 * Test MultiAccessLoginLink.
 *
 * @group multiaccess
 */
class MultiAccessLoginLinkTest extends TestCase {

  /**
   * Smoke test.
   */
  public function testSmoke() {
    $object = $this->getMockBuilder(MultiAccessLoginLink::class)
      // NULL = no methods are mocked; otherwise list the methods here.
      ->setMethods(NULL)
      ->disableOriginalConstructor()
      ->getMock();

    $this->assertTrue(is_object($object));
  }

}
