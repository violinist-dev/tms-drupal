<?php

namespace Drupal\met_api\Plugin\rest\resource;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Datetime\DateFormatter;

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
      ->sort('created', 'DESC')
      ->accessCheck(FALSE)
      ->execute();

    $nodes =  $storage->loadMultiple($nids);
    $new_nodes = [];
    $paragraph_fields_include = ['type', 'status', 'field_date', 'field_location', 'field_depth', 'field_magnitude', 'field_category', 'field_name', 'field_active'];
    foreach($nodes as $node) {
      $data = [];
      $data['id'] = $node->id();
      $data['title'] = $node->title->value;
      $data['body'] = $node->body->value;
      $data['active'] = $node->field_active->value;

      $fields = $node->field_event_type->referencedEntities();
      foreach($paragraph_fields_include as $field) {
        if (isset($fields[0]->$field)) {
          if ($field == 'type') {
            $data[$field] = $fields[0]->$field->target_id;
          } else if( $field == 'field_date') {
            $data['date'] = $fields[0]->$field->date->format('d/m/Y');
            $data['time'] = $fields[0]->$field->date->format('h:i a');
            $data['field_date'] = $fields[0]->$field->date->format('d/m/Y h:i a');
          } else {
            $data[$field] = $fields[0]->$field->value;
          }
        }
      }

      //check to see if any data in feel earthquake for this event
      if ($data['type'] == 'earthquake') {

        $str = \Drupal::service('entity_type.manager')->getStorage('met_feel_earthquake');
        $itemids = $str->getQuery()
          ->condition('field_event',$node->id())
          ->accessCheck(FALSE)
          ->execute();

        $feels =  $str->loadMultiple($itemids);
        $location = [];
        foreach ($feels as $field) {
            if (in_array($field->field_location->value, $location)) continue;
            $location[] = $field->field_location->value;
        }
        $data['feel'] = $location;
      }

      $new_nodes[$node->id()] = $data;
    }
    $build = ['#cache' => ['max-age' => 0]];

    return (new ResourceResponse($new_nodes, 200))->addCacheableDependency($build);
  }

  public function permissions() {
    return [];
  }

}
