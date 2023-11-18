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
            //echo "<pre>" . print_r($r, true) . "</pre>";
            if (is_array($r)) {

              if(!isset($r['temperature']))continue;
              //echo "<pre>" . print_r($r, true) . "</pre>";

              $weather_icon = $this->getWeatherIcon($r);

              $data['current'][$station] = [
                'location' => "$station",
                'icon' => "$weather_icon",
                'temperature' => "{$r['temperature']}",
                'humidity' => "{$r['humidity']}",
                'barometer' => "{$r['barometer']}", //pressure
                'wind_direction' => "{$r['wind_direction_label']}",
                'wind_speed' => "{$r['wind_speed']}",
                'visibility' => "{$r['visibility']}",
                'observed_date' => "{$r['observed_date']}",
                ];
            }
          }
        }
        fclose($fn);
    }

    //Add dummy data following

    //tbu
    $data['10day']['tbu'][] = [
      'icon' => '2',
      'day' => 'Monday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['tbu'][] = [
      'icon' => '1',
      'day' => 'Tuesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['tbu'][] = [
      'icon' => '3',
      'day' => 'Wednesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['tbu'][] = [
      'icon' => '5',
      'day' => 'Thursday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['tbu'][] = [
      'icon' => '2',
      'day' => 'Friday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['tbu'][] = [
      'icon' => '1',
      'day' => 'Saturday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];

    // Vv
    $data['10day']['vv'][] = [
      'icon' => '2',
      'day' => 'Monday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['vv'][] = [
      'icon' => '1',
      'day' => 'Tuesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['vv'][] = [
      'icon' => '3',
      'day' => 'Wednesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['vv'][] = [
      'icon' => '5',
      'day' => 'Thursday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['vv'][] = [
      'icon' => '2',
      'day' => 'Friday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['vv'][] = [
      'icon' => '1',
      'day' => 'Saturday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];

    //hpp
    $data['10day']['hpp'][] = [
      'icon' => '2',
      'day' => 'Monday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['hpp'][] = [
      'icon' => '1',
      'day' => 'Tuesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['hpp'][] = [
      'icon' => '3',
      'day' => 'Wednesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['hpp'][] = [
      'icon' => '5',
      'day' => 'Thursday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['hpp'][] = [
      'icon' => '2',
      'day' => 'Friday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['hpp'][] = [
      'icon' => '1',
      'day' => 'Saturday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];

    //nfo
    $data['10day']['nfo'][] = [
      'icon' => '2',
      'day' => 'Monday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['nfo'][] = [
      'icon' => '1',
      'day' => 'Tuesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['nfo'][] = [
      'icon' => '3',
      'day' => 'Wednesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['nfo'][] = [
      'icon' => '5',
      'day' => 'Thursday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['nfo'][] = [
      'icon' => '2',
      'day' => 'Friday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['nfo'][] = [
      'icon' => '1',
      'day' => 'Saturday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];

    //ntt
    $data['10day']['ntt'][] = [
      'icon' => '2',
      'day' => 'Monday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['ntt'][] = [
      'icon' => '1',
      'day' => 'Tuesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['ntt'][] = [
      'icon' => '3',
      'day' => 'Wednesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['ntt'][] = [
      'icon' => '5',
      'day' => 'Thursday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['ntt'][] = [
      'icon' => '2',
      'day' => 'Friday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['ntt'][] = [
      'icon' => '1',
      'day' => 'Saturday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];

    //eua
    $data['10day']['eua'][] = [
      'icon' => '2',
      'day' => 'Monday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['eua'][] = [
      'icon' => '1',
      'day' => 'Tuesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['eua'][] = [
      'icon' => '3',
      'day' => 'Wednesday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['eua'][] = [
      'icon' => '5',
      'day' => 'Thursday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['eua'][] = [
      'icon' => '2',
      'day' => 'Friday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];
    $data['10day']['eua'][] = [
      'icon' => '1',
      'day' => 'Saturday',
      'max_temp' => '38',
      'min_temp' => '25',
    ];

    //3HRS TBU
    $data['3hrs']['tbu'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'temp' => '26',
      'wind_direction' => 'SE',
      'wind_speed' => '5-15',
      'pressure' => '10000'
    ];
    // VV
    $data['3hrs']['vv'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'temp' => '26',
      'wind_direction' => 'SE',
      'wind_speed' => '5-15',
      'pressure' => '10000'
    ];
    // HPP
    $data['3hrs']['hpp'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'temp' => '26',
      'wind_direction' => 'SE',
      'wind_speed' => '5-15',
      'pressure' => '10000'
    ];
    // NTT
    $data['3hrs']['ntt'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'temp' => '26',
      'wind_direction' => 'SE',
      'wind_speed' => '5-15',
      'pressure' => '10000'
    ];
    // NFO
    $data['3hrs']['nfo'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'temp' => '26',
      'wind_direction' => 'SE',
      'wind_speed' => '5-15',
      'pressure' => '10000'
    ];
    // EUA
    $data['3hrs']['eua'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'temp' => '26',
      'wind_direction' => 'SE',
      'wind_speed' => '5-15',
      'pressure' => '10000'
    ];


    // 24 HOURS TBU
    $data['24hrs']['tbu'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'max_temp' => '25',
      'min_temp' => '17',
      'wind_direction' => 'SE',
      'wind_speed' => '10-15',
      'warning' => 'Strong Wind Warning for coastal waters and small craft advisory',
    ];

    $data['24hrs']['hpp'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'max_temp' => '25',
      'min_temp' => '17',
      'wind_direction' => 'SE',
      'wind_speed' => '10-15',
      'warning' => 'Strong Wind Warning for coastal waters and small craft advisory',
    ];

    $data['24hrs']['vv'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'max_temp' => '25',
      'min_temp' => '17',
      'wind_direction' => 'SE',
      'wind_speed' => '10-15',
      'warning' => 'Strong Wind Warning for coastal waters and small craft advisory',
    ];

    $data['24hrs']['nfo'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'max_temp' => '25',
      'min_temp' => '17',
      'wind_direction' => 'SE',
      'wind_speed' => '10-15',
      'warning' => 'Strong Wind Warning for coastal waters and small craft advisory',
    ];

    $data['24hrs']['ntt'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'max_temp' => '25',
      'min_temp' => '17',
      'wind_direction' => 'SE',
      'wind_speed' => '10-15',
      'warning' => 'Strong Wind Warning for coastal waters and small craft advisory',
    ];

    $data['24hrs']['eua'] = [
      'icon' => '1',
      'condition' => 'Partly Cloudy',
      'max_temp' => '25',
      'min_temp' => '17',
      'wind_direction' => 'SE',
      'wind_speed' => '10-15',
      'warning' => 'Strong Wind Warning for coastal waters and small craft advisory',
    ];


    //Create new CSV file and put the current weather data there
    $json_file_name = 'weather.json';
    $json_absolute_path = \Drupal::service('file_system')->realpath('public://' . $json_file_name);

    $data = serialize($data);
    file_put_contents($json_absolute_path, $data);

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
      switch($data['present_weather']) {
        case 'RA':
          return 7;
        case 'DZ':
          return 5;
        case 'TS':
          return 9;
        case 'SH':
          return 6;
        default:
          return 7;
      }
    } else {

      if (!isset($data['clouds'])) return 0;
      $total =  is_array($data['clouds']) ?  count($data['clouds']) - 1 : 0;
      switch($data['clouds'][$total]['amount']) {
        case 'FEW':
          return 1;
        case 'SCT':
          return 2;
        case 'BKN':
        case 'OVC':
          return 3;
        default:
          return 1;
      }
    }
  }
}
