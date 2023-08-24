<?php

namespace Drupal\met_api\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[AllowDynamicProperties]

/**
 * Provides the API resource for the mobile App
 *
 * @RestResource(
 *   id = "met_api_event_resource",
 *   label = @Translation("MET API Event Resouce"),
 *   uri_paths = {
 *      "canonical" = "/api/v1/event"
 *   }
 * )
 */
class EventResource extends ResourceBase {

  use StringTranslationTrait;

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, AccountProxyInterface $current_user) {

    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('MET API'),
      $container->get('current_user')
    );
  }

  public function get() {

    $storage = \Drupal::service('entity_type.manager')->getStorage('node');
    $nids = $storage->getQuery()
      ->condition('type','event')
      ->condition('field_active', 1)
      ->condition('status', 1)
      ->accessCheck(FALSE)
      ->execute();

    $nodes =  $storage->loadMultiple($nids);
    $new_nodes = [];
    $paragraph_fields_include = ['type', 'status', 'field_date', 'field_location', 'field_depth', 'field_magnitude', 'field_category'];
    foreach($nodes as $node) {
      $data = [];
      $data['id'] = $node->id();
      $data['title'] = $node->title->value;
      $data['body'] = $node->body->value;
      $data['active'] = $node->field_active->value;

      $fields = $node->field_event_type->referencedEntities();
      foreach($paragraph_fields_include as $field) {
        if (isset($fields[0]->$field)) {
          if($field == 'type') {
            $data['p'][$field] = $fields[0]->$field->target_id;
          } else {
            $data['p'][$field] = $fields[0]->$field->value;
          }
        }
      }

      $new_nodes[] = $data;
    }
    return new ResourceResponse($new_nodes, 200);
  }

  public function permissions() {
    return [];
  }

}
