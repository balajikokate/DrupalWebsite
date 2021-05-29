<?php

namespace Drupal\imdb_api;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;
use Imdb\Client\ApiDojo\ClientFactory;
use Imdb\Client\ApiDojo\Credentials;
use Imdb\Entities\TvShow;
use League\Uri\UriTemplate;
use Psr\Log\LoggerInterface;

/**
 * Class ImdbApiService.
 *
 * @method \Drupal\imdb_api\ImdbApiService createActor($string)
 * @method \Drupal\imdb_api\ImdbApiService createMovie($string)
 * @method \Drupal\imdb_api\ImdbApiService createTvShow($string)
 * @method \Drupal\imdb_api\ImdbApiService createEpisode($string)
 * @method \Drupal\imdb_api\ImdbApiService createVideoGame($string)
 */
class ImdbApiService implements ImdbApiInterface {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * A config object for the system performance configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  protected $client;
  protected $fetcher;
  protected $parser;

  private static $tvShows = [];

  /**
   * Constructs a new ImdbApiService object.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   A config factory for retrieving required config objects.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(ClientInterface $http_client, ConfigFactoryInterface $config_factory, LoggerInterface $logger) {
    $this->httpClient = $http_client;
    $this->config = $config_factory->get('imdb_api.imdbapiconfig');
    $this->logger = $logger;

    $this->createConfiguration();
  }

  private function getApi() {
    return $this->config->get('api');
  }

  public function createConfiguration() {
    switch ($this->getApi()) {
      case 'rapidapi':
        $key = $this->config->get('rapidapi_api_key');
        $base_url = $this->config->get('rapidapi_base_url');
        $use_query_string = $this->config->get('rapidapi_use_query_string');

        if ($key && $base_url && $use_query_string) {
          $fetcher = $this->config->get('rapidapi_fetcher');
          $parser = $this->config->get('rapidapi_parser');

          $uri = new UriTemplate($base_url);

          $credentials = new Credentials($uri, $key, $use_query_string);

          $this->client = new ClientFactory($this->httpClient, $credentials);
          $this->parser = new $parser();

          if (($cache = $this->config->get('rapidapi_cache')) && $cache != 'none') {
            $this->setUpWithCaching($fetcher, $cache);
          }
          else {
            $this->fetcher = new $fetcher($this->parser, $this->client);
          }
        }

        break;
    }
  }

  private function setUpWithCaching($fetcher, $cache) {
    $cacheClient = new $cache();

    $timeout = $this->config->get('rapidapi_cache_timeout');
    $interval = $this->config->get('rapidapi_cache_interval');

    $cacheConfig = [
      'host' => $this->config->get('rapidapi_cache_host'),
      'port' => $this->config->get('rapidapi_cache_port'),
      'timeout' => $timeout ? $timeout : NULL,
      'interval' => $interval ? $interval : NULL,
    ];

    $this->fetcher = new $fetcher($this->parser, $this->client, $cacheClient, $cacheConfig);
  }

  public function getFetcher() {
    return $this->fetcher;
  }

  public function __call($name, $arguments) {
    if (preg_match('/^create(.+)$/', $name, $matches)) {
      $class = '\Imdb\Entities\\' . $matches[1];

      // Checking if there is matching method to create IMDB entity.
      if (!class_exists($class)) {
        return FALSE;
      }

      try {
        return new $class($this->fetcher, reset($arguments));
      }
      catch (\Exception $exception) {
        $this->logger->warning($exception->getMessage());
      }
    }

    return FALSE;
  }

  public function createImdbEntity($string, $type) {
    // @TODO get rid of hardcoded options.
    switch ($type) {
      case 'actor':
        return $this->createActor($string);
      case 'movie':
        return $this->createMovie($string);
      case 'episode':
        return $this->createEpisode($string);
      case 'tv_show':
      case 'tvshow':
        return $this->createTvShow($string);
      case 'video_game':
      case 'videogame':
        return $this->createVideoGame($string);
    }

    return FALSE;
  }

  public static function getTvShowSeasons($tv_show) {
    $pure_id = $tv_show->getPureId();
    self::$tvShows[$pure_id] = self::$tvShows[$pure_id] ?? $tv_show->getSeasons();

    return self::$tvShows[$pure_id];
  }

  public function getTvShowFirstSeasonId(TvShow $tv_show) {
    $seasons = self::getTvShowSeasons($tv_show);

    return reset($seasons)->season;
  }

  public function getTvShowSeasonLastEpisodeId(TvShow $tv_show, int $season_id) {
    $id = NULL;

    foreach (self::getTvShowSeasons($tv_show) as $season) {
      if ($season->season == $season_id) {
        $id = end($season->episodes)->episode;
        break;
      }
    }

    return $id;
  }
}
