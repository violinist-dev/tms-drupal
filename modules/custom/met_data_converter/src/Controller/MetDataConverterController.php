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
  public function __invoke(): array
  {

    $files_urls = [
      'tbu' => 'https://met.gov.to/metars/fmtmetar.txt',
      'hpp' => 'https://met.gov.to/metars/hapmetar.txt',
      'vv' => 'https://met.gov.to/metars/vvumetar.txt',
      'nfo' => 'https://met.gov.to/metars/nfometar.txt',
      'ntt' => 'https://met.gov.to/metars/nttmetar.txt',
      'eua' => 'https://met.gov.to/metars/fmtmetar.txt',
    ];
    $data = [];
    $metar_lines = [];
    foreach ($files_urls as $station => $data_file) {

      $fn = fopen($data_file, 'r');

      while (!feof($fn)) {
        $result = fgets($fn);
        if (!$result || empty(trim($result))) continue;
        $initial = substr($result, 0, 5);
        if ($initial == 'METAR' || $initial == 'SPECI') {
          $metar_lines[] = $result;
        }
      }

    fclose($fn);

    if (count($metar_lines) > 0) {

       $total_lines = count($metar_lines);
       $result = $metar_lines[$total_lines - 1];
        $token = substr($result, 5);
        $metar = new Metar(trim($token));
        $r = $metar->parse();

        if (is_array($r)) {

          if (!isset($r['temperature'])) continue;
          //echo "<pre>" . print_r($r, true) . "</pre>";

          $weather_icon = $this->getWeatherIcon($r);

          //Convert visibility from m to km
          $visibility = ($r['visibility'] > 0) ? $r['visibility'] / 1000 : $r['visibility'];

          //Convert knots to k/h
          $wind_speed = $r['wind_speed'] > 0 ? $r['wind_speed'] * 1.852 : $r['wind_speed'];

          $data[$station] = [
            'location' => "$station",
            'icon' => "$weather_icon",
            'temperature' => "{$r['temperature']}",
            'humidity' => "{$r['humidity']}",
            'barometer' => "{$r['barometer']}", //pressure
            'wind_direction' => "{$r['wind_direction_label']}",
            'wind_speed' => "$wind_speed",
            'visibility' => "$visibility",
            'observed_date' => "{$r['observed_date']}",
          ];
        }
      }
    }


    //Create new CSV file and put the current weather data there
    $csv_file_name = 'weather_metar.csv';
    $csv_absolute_path = \Drupal::service('file_system')->realpath('private://' . $csv_file_name);

    $fp = fopen($csv_absolute_path, 'w'); // open in write only mode (write at the start of the file)
    fputcsv($fp, ['current']);
    foreach ($data as $location => $value) {
      fputcsv($fp, $value);
    }
    fclose($fp);

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

    if (is_array($data['present_weather'])) {
      if (count($data['present_weather']) > 0) {
        $condition = $data['present_weather'][0];
        if (is_array($condition)) {
          $type = $condition['types'][0];
          $intensity = $condition['intensity'];
          if (!empty($type) && !empty($intensity)) {
            switch ($type) {
              case 'RA': //rain
                if ($intensity == '-') return 5; //light rain
                if ($intensity == '+') return 7; //heavy rain
              break;
              case 'DZ':
              case 'SH':
                return 5;
              case 'TS':
                return 9;
            }
          }
        }
      }
    } else {
      if (!isset($data['clouds'])) return 0;
      $total =  is_array($data['clouds']) ?  count($data['clouds']) - 1 : 0;
      switch($data['clouds'][$total]['amount']) {
        case 'FEW': //few cloud
          return 1;
        case 'SCT': //scattered cloud
          return 2;
        case 'BKN': //broken cloud
        case 'OVC': //overcast cloud
          return 3;
        default:
          return 0;
      }
    }
  }
}
