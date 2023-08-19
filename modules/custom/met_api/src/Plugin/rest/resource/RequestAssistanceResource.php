<?php

namespace Drupal\met_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides the API resource for the mobile App
 *
 * @RestResource(
 *   id = "met_api_request_assistance_resource",
 *   label = @Translation("MET API Request Assistance Resouce"),
 *   uri_paths = {
 *      "create" = "/api/v1/request-assistance"
 *   }
 * )
 */
class RequestAssistanceResource extends ResourceBase {

  /**
   * Responds to entity POST requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function post() {
    $response = ['message' => 'Hello, this is a request assistance rest service'];
    return new ResourceResponse($response);
  }

}
