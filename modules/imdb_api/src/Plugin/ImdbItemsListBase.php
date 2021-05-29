<?php

namespace Drupal\imdb_api\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\imdb_api\ImdbApiInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for IMDB items list plugins.
 */
abstract class ImdbItemsListBase extends PluginBase implements ImdbItemsListInterface, ContainerFactoryPluginInterface {

  /**
   * The IMDB API service.
   *
   * @var \Drupal\imdb_api\ImdbApiInterface
   */
  protected $imdbApi;

  /**
   * The list.
   *
   * @var array
   */
  protected $list;

  /**
   * Constructs a \Drupal\Component\Plugin\PluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $imdb_api
   *   The IMDB API service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ImdbApiInterface $imdb_api) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->imdbApi = $imdb_api;
    $this->list = $this->init();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('imdb_api.imdb_api')
    );
  }

  private function init() {
    $entities = [];

    $options = $this->configuration['options'] ?? [];
    $limit = $this->configuration['limit'] ?? NULL;

    $class = $this->pluginDefinition['entityClass'];
    $method = $this->pluginDefinition['method'];

    $raw_list = $class::$method($this->imdbApi->getFetcher(), $options);
    if (!empty($this->configuration['shuffle'])) {
      shuffle($raw_list);
    }
    $raw_list = isset($limit) ? array_slice($raw_list, 0, $limit) : $raw_list;

    foreach ($raw_list as $item) {

      if (($id = $this->handleItem($item)) && preg_match('/^\\\\Imdb\\\\Entities\\\\(.+)$/', $class, $match)) {
        $entities[$id] = $this->imdbApi->createImdbEntity($id, strtolower($match[1]));
      }
    }

    return $entities;
  }

  public function getList() {
    return $this->list;
  }

  abstract public function handleItem($item);

}
