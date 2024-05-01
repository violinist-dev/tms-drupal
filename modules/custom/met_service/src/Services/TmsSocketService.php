<?php

namespace Drupal\met_service\Services;

use Drupal\Core\Config\ConfigFactory;
use Paragi\PhpWebsocket\Client;
use Paragi\PhpWebsocket\ConnectionException;

class TmsSocketService {

  protected int $port;
  protected string $host;


  public function __construct() {

    $config = \Drupal::configFactory()->getEditable('met_service.settings');

    $this->port = $config->get('port'); // 8080; //5123
    $this->host = $config->get('host'); // 'host.docker.internal'; //'host.docker.internal';  //app.met.gov.to
  }

  public function send($payload) {

    $payload = json_encode($payload);
    try {
      $str_err = '';
      $sp = new Client($this->host,$this->port, '', $str_err, 10, true);
      $sp->write($payload);
      return true;
    } catch (ConnectionException $e) {
      return false;
    }
  }
}
