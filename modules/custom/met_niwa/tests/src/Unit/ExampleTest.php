<?php

declare(strict_types=1);

namespace Drupal\Tests\met_niwa\Unit;

use Drupal\met_niwa\Controller\MetNiwaController;
use Drupal\Tests\UnitTestCase;

/**
 * Test description.
 *
 * @group met_niwa
 */
final class ExampleTest extends UnitTestCase {

  protected $metNiwaController;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // @todo Mock required classes here.
    $this->metNiwaController = new MetNiwaController();
  }

  /**
   * Tests something.
   */
  public function testSomething(): void {
    //self::assertTrue(TRUE, 'This is TRUE!');
    $this->assertTrue(TRUE, "This is true");
  }

  /**
   * Test the conversion function degreeToCompass
   * @return void
   */
  public function testDegreeToCompass(): void {
    $result = $this->metNiwaController->degreeToCompass(45);
    self::assertEquals('NE', $result);
  }

}
