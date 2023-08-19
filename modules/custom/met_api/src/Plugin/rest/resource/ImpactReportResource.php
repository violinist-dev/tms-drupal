<?php

namespace Drupal\met_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides the API resource for the mobile App
 *
 * @RestResource(
 *   id = "met_api_impact_report_resource",
 *   label = @Translation("MET API Impact Report Resouce"),
 *   uri_paths = {
 *      "create" = "/api/v1/impact-report"
 *   }
 * )
 */
class ImpactReportResource extends ResourceBase {

  public function post() {
    $response = ['message' => 'Hello, this is an impact report rest service'];
    return new ResourceResponse($response);
  }

}
