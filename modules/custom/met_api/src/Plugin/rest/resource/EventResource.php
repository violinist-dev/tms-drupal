<?php

namespace Drupal\met_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

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

  public function get() {
    $response = ['message' => 'Hello, this is an event rest service'];
    return new ResourceResponse($response);
  }

}
