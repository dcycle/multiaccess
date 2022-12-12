<?php

namespace Drupal\Tests\multiaccess\Unit;

use Drupal\multiaccess\MultiAccess;
use PHPUnit\Framework\TestCase;

/**
 * Test MultiAccess.
 *
 * @group multiaccess
 */
class MultiAccessTest extends TestCase {

  /**
   * Test for extractDomainFromUrl().
   *
   * @param string $message
   *   The test message.
   * @param string $input
   *   The input.
   * @param string $expected
   *   The expected result.
   *
   * @cover ::extractDomainFromUrl
   * @dataProvider providerExtractDomainFromUrl
   */
  public function testExtractDomainFromUrl(string $message, string $input, string $expected) {
    $object = $this->getMockBuilder(MultiAccess::class)
      // NULL = no methods are mocked; otherwise list the methods here.
      ->setMethods(NULL)
      ->disableOriginalConstructor()
      ->getMock();

    $output = $object->extractDomainFromUrl($input);

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
   * Provider for testExtractDomainFromUrl().
   */
  public function providerExtractDomainFromUrl() {
    return [
      [
        'message' => 'Empty URL',
        'input' => '',
        'expected' => '',
      ],
      [
        'message' => 'Base case',
        'input' => 'whatever://something:123/else?abc=def',
        'expected' => 'something',
      ],
      [
        'message' => 'Numbers',
        'input' => 'whatever://0.0.0.0:123/else?abc=def',
        'expected' => '0.0.0.0',
      ],
    ];
  }

}
