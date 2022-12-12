<?php

namespace Drupal\Tests\multiaccess\Integration\Unit;

use Drupal\multiaccess\Integration\IntegrationSource;
use PHPUnit\Framework\TestCase;

/**
 * Test IntegrationSource.
 *
 * @group multiaccess
 */
class IntegrationSourceTest extends TestCase {

  /**
   * Test for fixLoginLinkDomain().
   *
   * @param string $message
   *   The test message.
   * @param string $input
   *   The input.
   * @param string $expected
   *   The expected result.
   *
   * @cover ::fixLoginLinkDomain
   * @dataProvider providerFixLoginLinkDomain
   */
  public function testFixLoginLinkDomain(string $message, string $input, string $expected) {
    $object = $this->getMockBuilder(IntegrationSource::class)
      ->setMethods([
        'getPublicUrl',
      ])
      ->disableOriginalConstructor()
      ->getMock();

    $object->method('getPublicUrl')
      ->willReturn('http://something');

    $output = $object->fixLoginLinkDomain($input);

    $expected = 'http://something' . $expected;

    if ($output != $expected) {
      print_r([
        'message' => $message,
        'output' => $output,
        'expected' => $expected,
      ]);
    }

    $this->assertTrue($output == $expected, $message);
  }

  /**
   * Provider for testFixLoginLinkDomain().
   */
  public function providerFixLoginLinkDomain() {
    return [
      [
        'message' => 'Base case',
        'input' => 'http://default/user/blabla',
        'expected' => '/user/blabla',
      ],
      [
        'message' => 'Base case (https)',
        'input' => 'https://default/user/blabla',
        'input' => 'https://default/user/blabla',
        'expected' => '/user/blabla',
      ],
      [
        'message' => 'Valid domain',
        'input' => 'https://default.something.example.com/user/blabla',
        'input' => 'https://default.something.example.com/user/blabla',
        'expected' => '/user/blabla',
      ],
    ];
  }

}
