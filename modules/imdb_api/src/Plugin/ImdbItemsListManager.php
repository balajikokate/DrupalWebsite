<?php

namespace Drupal\imdb_api\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the IMDB items list plugin manager.
 */
class ImdbItemsListManager extends DefaultPluginManager {

  /**
   * Constructs a new ImdbItemsListManager object.
   *
   * @param string $type
   *   The plugin type.
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(string $type, \Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/ImdbItemsList/' . $type, $namespaces, $module_handler, 'Drupal\imdb_api\Plugin\ImdbItemsListInterface', 'Drupal\imdb_api\Annotation\ImdbItemsList');

    $this->alterInfo('imdb_api_imdb_items_' . $type . '_list_info');
    $this->setCacheBackend($cache_backend, 'imdb_api_imdb_items_' . $type . '_list_plugins');
  }

}
