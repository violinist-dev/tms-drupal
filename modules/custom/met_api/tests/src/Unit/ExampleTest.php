<?php

declare(strict_types=1);

namespace Drupal\Tests\met_api\Unit;

use Drupal\Tests\UnitTestCase;

/**
 * Test description.
 *
 * @group met_api
 */
final class AccountResourceTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    

    $container = new Container();
    $container->set('string_translation', $this->getStringTranslationStub());

    $renderer = $this->getMockBuilder(Renderer::class)
      ->disableOriginalConstructor()
      ->getMock();
    $renderer
      ->method('render')
      ->willReturn('Hello world');

    $container->set('renderer', $renderer);

    // Mock entity type manager.
    $this->entityTypeManager = $this->getMockBuilder(EntityTypeManagerInterface::class)
      ->disableOriginalConstructor()
      ->getMock();

    // Mock a node and add the label to it.
    $node = $this->getMockBuilder(Node::class)
      ->disableOriginalConstructor()
      ->getMock();
    $node->expects($this->any())
      ->method('label')
      ->willReturn('shaken not stirred');

    $node->expects($this->any())
      ->method('access')
      ->willReturn(TRUE);

    $node->expects($this->any())
      ->method('id')
      ->willReturn(1);


    // @todo figure out how to add a field to a mock node.
    /*$field_description = new \stdClass();

    $field_description->value = 'This is a description';

    $node->set('field_description', $field_description);*/


    $node_storage = $this->getMockBuilder(EntityStorageInterface::class)
      ->disableOriginalConstructor()
      ->getMock();
    $node_storage->expects($this->any())
      ->method('load')
      ->willReturn($node);
    $this->entityTypeManager->expects($this->any())
      ->method('getStorage')
      ->willReturn($node_storage);

    $this->uuid = $this->getMockBuilder(UuidInterface::class)
      ->disableOriginalConstructor()
      ->getMock();

    $container->set('entity_type.manager', $this->entityTypeManager);

    $entity_repository = $this->getMockBuilder(EntityRepository::class)
      ->disableOriginalConstructor()
      ->getMock();
    $entity_repository->expects($this->any())
      ->method('getTranslationFromContext')
      ->willReturn($node);

    $container->set('entity.repository', $entity_repository);

    \Drupal::setContainer($container);
    $this->form = MyForm::create($container);

  }

  /**
   * Tests something.
   */
  public function testSomething(): void {
    self::assertTrue(TRUE, 'This is TRUE!');
    self::assertEquals(5, 4);
  }

}
