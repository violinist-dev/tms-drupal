<?php

namespace Drupal\met_api\Plugin\rest\resource;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\taxonomy\Entity\Term;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the API resource for the Traditional Knowledge in mobile App
 *
 * @RestResource(
 *   id = "met_api_tk",
 *   label = @Translation("MET Traditional Knowledge Resouce"),
 *   uri_paths = {
 *     "create" = "/api/v1/tk",
 *     "canonical" = "/api/v1/tk/{flag}"
 *   }
 * )
 */
class TkResource extends ResourceBase {
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

  public function get($flag = 'tk')
  {
      switch ($flag) {
        case 'tk':
          return $this->getTk();
        case 'ind':
          return $this->getIndicators();
      }
  }

  public function post() {

  }

  public function getIndicators() {

    $vid = 'tk_indicators';
    $terms =\Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);

    $data = [];
    $weight = 0;
    foreach($terms as $term) {
      $term = Term::load($term->tid);

      //get indicator photo
      $fid = $term->get('field_photo')->target_id;
      $file = File::load($fid);
      $photo_url = $file->createFileUrl(false);

      $desc = $term->get('description')->getValue()[0]['value'];

      $data[] = [
        'name' => $term->getName(),
        'desc' => strip_tags($desc),
        'photo' => $photo_url,
        'id' => (int)$term->id(),
        'weight' => ++$weight,
      ];
    }

    $build = [
      '#cache' => [
        'tags' => ['taxonomy_term_list:tk_indicators']
        ]
    ];

    return (new ResourceResponse($data, 200))->addCacheableDependency(CacheableMetadata::createFromRenderArray($build));
  }

  public function getTk() {

    $storage = \Drupal::service('entity_type.manager')->getStorage('met_tk');
    $items = $storage->getQuery()
      ->condition('status', 1)
      ->accessCheck(FALSE)
      ->sort('created', 'DESC')
      ->range(0, 100)
      ->execute();

    $items =  $storage->loadMultiple($items);
    $new_items = [];
    foreach($items as $item) {

      $data = [];

      //process location
      $lat = 0;
      $lon = 0;
      if ($item->field_geo_location->value != "") {
        list($lat, $lon) = explode(", ", $item->field_geo_location->value);
      }
      //get indicator from taxonomy
      $termid = $item->get('field_idicator')->target_id;
      $term = Term::load($termid);

      //get indicator photo
      $fid = $term->get('field_photo')->target_id;
      $file = File::load($fid);
      $photo_url = $file->createFileUrl(false);

      $data['indicator'] = [
        'name' => $term->getName(),
        'desc' => $term->get('description')->getValue()[0]->value,
        'photo' => $photo_url,
        'id' => (int)$term->id(),
      ];

      //get tk photo
      $fid = $item->get('field_photo')->target_id;
      $file = File::load($fid);
      $photo = $file->createFileUrl(false);

      $data['id'] = (int)$item->id();
      $data['lat'] = (double)$lat;
      $data['lon'] = (double)$lon;
      $data['time'] = \Drupal::service('date.formatter')->format($item->created->value, 'custom', 'h:i a'); //<-- use time in the image
      $data['date'] = \Drupal::service('date.formatter')->format($item->created->value, 'custom', 'd/m/Y'); // <-- use date in the image
      $data['timestamp'] = $item->created->value;
      $data['photo'] = $photo;
      $data['title'] = $item->get('label')->value;

      $new_items[] = $data;
    }
    $build = ['#cache' => ['max-age' => 0]];

    return (new ResourceResponse($new_items, 200))->addCacheableDependency($build);
  }

  public function permissions() {
    return [];
  }

}
