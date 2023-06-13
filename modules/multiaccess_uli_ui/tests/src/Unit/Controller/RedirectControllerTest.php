<?php

namespace Drupal\Tests\multiaccess_uli_ui\Controller\Unit;

use Drupal\multiaccess_uli_ui\Controller\RedirectController;
use PHPUnit\Framework\TestCase;

/**
 * Test RedirectController.
 *
 * @group multiaccess
 */
class RedirectControllerTest extends TestCase {

  /**
   * Test for removeDomainAndPort().
   *
   * @param string $message
   *   The test message.
   * @param string $input
   *   The input.
   * @param string $expected
   *   The expected result.
   *
   * @cover ::removeDomainAndPort
   * @dataProvider providerRemoveDomainAndPort
   */
  public function testRemoveDomainAndPort(string $message, string $input, string $expected) {
    $object = $this->getMockBuilder(RedirectController::class)
      // NULL = no methods are mocked; otherwise list the methods here.
      ->setMethods(NULL)
      ->disableOriginalConstructor()
      ->getMock();

    $output = $object->removeDomainAndPort($input);

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
   * Provider for testRemoveDomainAndPort().
   */
  public function providerRemoveDomainAndPort() {
    return [
      [
        'message' => 'Empty string',
        'input' => '',
        'expected' => '',
      ],
      [
        'message' => 'Path',
        'input' => '/hello/world',
        'expected' => '/hello/world',
      ],
      [
        'message' => 'Path with domain and port',
        'input' => 'http%3A//localhost%3A56127/admin/index',
        'expected' => '/admin/index',
      ],
    ];
  }

}
