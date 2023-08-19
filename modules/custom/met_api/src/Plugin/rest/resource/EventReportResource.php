<?php

namespace Drupal\met_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides the API resource for the mobile App
 *
 * @RestResource(
 *   id = "met_event_report_api_resource",
 *   label = @Translation("MET API Event Report Resouce"),
 *   uri_paths = {
 *      "create" = "/api/v1/event-report"
 *   }
 * )
 */
class EventReportResource extends ResourceBase {

  public function post() {
    $response = ['message' => 'Hello, this is an event report rest service'];
    return new ResourceResponse($response);
  }

}
