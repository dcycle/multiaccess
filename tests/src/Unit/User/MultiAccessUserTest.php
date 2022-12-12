<?php

namespace Drupal\Tests\multiaccess\User\Unit;

use Drupal\multiaccess\User\MultiAccessUser;
use PHPUnit\Framework\TestCase;

/**
 * Test MultiAccessUser.
 *
 * @group multiaccess
 */
class MultiAccessUserTest extends TestCase {

  /**
   * Smoke test.
   */
  public function testSmoke() {
    $object = $this->getMockBuilder(MultiAccessUser::class)
      // NULL = no methods are mocked; otherwise list the methods here.
      ->setMethods(NULL)
      ->disableOriginalConstructor()
      ->getMock();

    $this->assertTrue(is_object($object));
  }

}
