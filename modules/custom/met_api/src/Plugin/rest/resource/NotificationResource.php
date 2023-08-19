<?php

namespace Drupal\met_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides the API resource for the mobile App
 *
 * @RestResource(
 *   id = "met_api_notification_resource",
 *   label = @Translation("MET API Notification Resouce"),
 *   uri_paths = {
 *      "canonical" = "/api/v1/notification"
 *   }
 * )
 */
class NotificationResource extends ResourceBase {

  public function get() {
    $response = ['message' => 'Hello, this is a notification rest service'];
    return new ResourceResponse($response);
  }

}
