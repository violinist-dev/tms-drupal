<?php declare(strict_types = 1);

namespace Drupal\met_data_converter\Controller;

use Drupal\met_data_converter\Metar;
use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for Met data converter routes.
 */
final class MetDataConverterController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $files_urls = [
      'tongatapu' => 'https://met.gov.to/metars/fmtmetar.txt',
      'haapai' => 'https://met.gov.to/metars/hapmetar.txt',
      'vavau' => 'https://met.gov.to/metars/vvumetar.txt',
      'niuafoou' => 'https://met.gov.to/metars/nfometar.txt',
      'niuatoputapu' => 'https://met.gov.to/metars/nttmetar.txt'
    ];
    $data = [];

    $field_data = [
      'wind_speed' => '',
      'wind_direction_label' => '',
      'visibility' => '',
      'temperature' => '',
      'humidity' => '',
      'barometer' => '',
      'observed_date' => '',
      'dew_point' => ''
    ];
    foreach($files_urls as $data_file) {

        $fn = fopen($data_file, 'r');
        while( !feof($fn)) {
          $result = fgets($fn);
          if (!$result || empty(trim($result))) continue;
          $initial = substr($result, 0, 5);
          if ($initial == 'METAR') {
            $token = substr($result, 5);
            $metar = new Metar(trim($token));
            $r = $metar->parse();
            if (is_array($r)) {
              $data[] = $r; //array_intersect_key($r, $field_data);
            }
          }
        }
        fclose($fn);
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => "<pre>" . print_r($data, true) . "</pre>",
      '#cache' => [
        'max-age' => 0
      ],
    ];

    return $build;
  }

}
