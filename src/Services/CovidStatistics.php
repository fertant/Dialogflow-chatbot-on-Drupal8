<?php

namespace Drupal\chatbot\Services;

use GuzzleHttp\Client;

/**
 * Class CovidStatistics.
 */
class CovidStatistics {

  /**
   * Guzzle\Client instance.
   *
   * @var \Guzzle\Client
   */
  protected $httpClient;

  /**
   * Constructs a new ChatbotDetectIntent object.
   * 
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(Client $http_client) {
    $this->httpClient = $http_client;
  }

  /**
   * Request COVID-19 statistics.
   */
  private function getStatistics() {
    $request = $this->httpClient->request('GET', 'https://coronavirus-tracker-api.herokuapp.com/v2/locations');
    $this->statistics = json_decode($request->getBody());
  }

  /**
   * Prepare request.
   * 
   * @param string request test 
   */
  public function substituteCountryStatistics(string $country) {
    $this->getStatistics();
    foreach ($this->statistics->locations as $item) {
      if ($item->country == $country) {
        $filtered = $item;
        break;
      }
    }
    return $filtered;
  }

}
