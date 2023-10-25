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
      'tbu' => 'https://met.gov.to/metars/fmtmetar.txt',
      'hpp' => 'https://met.gov.to/metars/hapmetar.txt',
      'vv' => 'https://met.gov.to/metars/vvumetar.txt',
      'nfo' => 'https://met.gov.to/metars/nfometar.txt',
      'ntt' => 'https://met.gov.to/metars/nttmetar.txt',
      'eua' => 'https://met.gov.to/metars/fmtmetar.txt',
    ];
    $data = [];

    foreach($files_urls as $station => $data_file) {

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
              $weather_icon = $this->getWeatherIcon($r);
              $data[$station] = [
                $station,
                $weather_icon,
                $r['temperature'],
                $r['humidity'],
                $r['barometer'], //pressure
                $r['wind_direction_label'],
                $r['wind_speed'],
                $r['visibility'],
                $r['observed_date'],
                ];
            }
          }
        }
        fclose($fn);
    }

    //Create new CSV file and put the current weather data there
    $csv_file_name = 'weather_current.csv';
    $absolute_path = \Drupal::service('file_system')->realpath('public://' . $csv_file_name);
    $file = fopen($absolute_path, "w");

    fputcsv($file, ['current']);
    foreach($data as $fields) {
      fputcsv($file, $fields);
    }
    fclose($file);

    $build['content'] = [
      '#type' => 'item',
      '#markup' => "Weather forecast has been updated",
      '#cache' => [
        'max-age' => 0
      ],
    ];

    return $build;
  }

  function getWeatherIcon($data) {
    if(!empty($data['present_weather'])) {
      switch($data['resent_weather']) {
        case 'RA':
          return 7;
        case 'DZ':
          return 5;
        case 'TS':
          return 9;
        case 'SH':
          return 6;
      }
    } else {
      switch($data['clouds'][0]['amount']) {
        case 'FEW':
          return 1;
        case 'SCT':
          return 2;
        case 'BKN':
        case 'OVC':
          return 3;
      }
    }
  }
}
