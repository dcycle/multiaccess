<?php

namespace Drupal\Tests\multiaccess\Integration\Unit;

use Drupal\multiaccess\Integration\IntegrationSourceFactory;
use PHPUnit\Framework\TestCase;

/**
 * Test IntegrationSourceFactory.
 *
 * @group multiaccess
 */
class IntegrationSourceFactoryTest extends TestCase {

  /**
   * Smoke test.
   */
  public function testSmoke() {
    $object = $this->getMockBuilder(IntegrationSourceFactory::class)
      // NULL = no methods are mocked; otherwise list the methods here.
      ->setMethods(NULL)
      ->disableOriginalConstructor()
      ->getMock();

    $this->assertTrue(is_object($object));
  }

}
